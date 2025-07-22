<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuanPelatihan;
use App\Models\DaftarHadirPelatihan;
use App\Models\DaftarHadirPelatihanStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class DaftarHadirPelatihanController extends Controller
{
    /* ===============================================================
     |  INDEX – list pelatihans eligible for attendance
     |  Only show pelatihans whose Surat Tugas has been fully accepted.
     * ===============================================================*/
    public function index(Request $request)
    {
        $user = Auth::user();

        $q = SuratPengajuanPelatihan::query()
            ->with(['suratTugas', 'participants.user'])
            ->whereHas('suratTugas', fn($st) => $st->where('is_accepted', 1));

        if (!$this->userCanSeeAll($user)) {
            $q->whereHas('participants', fn($p) => $p->where('user_id', $user->id));
        }

        if ($s = trim($request->q ?? '')) {
            $q->where(function ($w) use ($s) {
                $w->where('kode_pelatihan', 'like', "%{$s}%")
                  ->orWhere('judul', 'like', "%{$s}%");
            });
        }

        $pelatihans = $q->orderByDesc('tanggal_mulai')->get();

        return view('pages.training.daftarhadirpelatihan.index', compact('pelatihans'));
    }

    /* ===============================================================
     |  SHOW – training-level page (list all days)
     * ===============================================================*/
    public function show($pelatihanId)
    {
        $user = Auth::user();

        $pelatihan = SuratPengajuanPelatihan::with([
            'suratTugas',
            'daftarHadirStatus',
        ])->findOrFail($pelatihanId);

        if (!$this->isSuratTugasAccepted($pelatihan)) {
            abort(Response::HTTP_FORBIDDEN, 'Daftar hadir belum dapat diisi: Surat Tugas belum disetujui.');
        }

        if (!$this->userCanAccessPelatihan($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->ensureDayStatusRows($pelatihan);
        $pelatihan->load('daftarHadirStatus');

        return view('pages.training.daftarhadirpelatihan.show', compact('pelatihan'));
    }

    /* ===============================================================
     |  DAY – detailed attendance editor for a specific date
     * ===============================================================*/
    public function day($pelatihanId, $date)
    {
        $user = Auth::user();

        $pelatihan = SuratPengajuanPelatihan::with([
            'suratTugas',
            'participants.user',
            'participants.jabatan',
            'participants.department',
        ])->findOrFail($pelatihanId);

        if (!$this->isSuratTugasAccepted($pelatihan)) {
            abort(Response::HTTP_FORBIDDEN, 'Daftar hadir belum dapat diisi: Surat Tugas belum disetujui.');
        }

        if (!$this->userCanAccessPelatihan($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $day = Carbon::parse($date)->toDateString();

        $status = DaftarHadirPelatihanStatus::firstOrCreate([
            'pelatihan_id' => $pelatihan->id,
            'date'         => $day,
        ]);

        $attendances = DaftarHadirPelatihan::where('pelatihan_id', $pelatihan->id)
            ->whereDate('date', $day)
            ->get();

        return view('pages.training.daftarhadirpelatihan.day', [
            'pelatihan'   => $pelatihan,
            'date'        => Carbon::parse($day),
            'status'      => $status,
            'attendances' => $attendances,
        ]);
    }

    /* ===============================================================
     |  IMPORT – parse CSV and directly save to DB
     * ===============================================================*/
    public function import(Request $request, $pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::with(['suratTugas', 'participants.user'])->findOrFail($pelatihanId);

        if (!$this->isSuratTugasAccepted($pelatihan)) {
            return redirect()
                ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $date])
                ->with('error', 'Surat Tugas belum disetujui; tidak dapat impor daftar hadir.');
        }

        if (!$this->userCanManageAttendance($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $day = Carbon::parse($date)->toDateString();

        DaftarHadirPelatihanStatus::firstOrCreate([
            'pelatihan_id' => $pelatihan->id,
            'date'         => $day,
        ]);

        $participantsByReg = $pelatihan->participants
            ->filter(fn($p) => $p->registration_id)
            ->mapWithKeys(fn($p) => [trim($p->registration_id) => $p]);

        $rows = $this->parseAttendanceFile($request->file('file'));

        [$importedCount, $skippedCount, $preview] = $this->buildImportPreviewArray(
            $rows,
            $pelatihan,
            $day,
            $participantsByReg
        );

        DB::transaction(function () use ($preview, $pelatihan, $participantsByReg, $day) {
            foreach ($preview as $reg => $data) {
                $participant = $participantsByReg[$reg];
                $att = DaftarHadirPelatihan::firstOrNew([
                    'pelatihan_id'   => $pelatihan->id,
                    'participant_id' => $participant->id,
                    'date'           => $day,
                ]);

                $att->status              = $data['status'] ?? 'hadir';
                $att->check_in_time       = $data['check_in_time'] ?? null;
                $att->check_in_timestamp  = $data['check_in_timestamp'] ?? null;
                $att->check_in_photo      = $data['check_in_photo'] ?? null;
                $att->check_out_time      = $data['check_out_time'] ?? null;
                $att->check_out_timestamp = $data['check_out_timestamp'] ?? null;
                $att->check_out_photo     = $data['check_out_photo'] ?? null;
                $att->save();
            }
        });

        return redirect()
            ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
            ->with('success', "Impor selesai. Disimpan: {$importedCount}, Dilewati: {$skippedCount}.");
    }

    /* ===============================================================
     |  SAVE – persist manual edits
     * ===============================================================*/
    public function save(Request $request, $pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::with('participants')->findOrFail($pelatihanId);

        if (!$this->userCanManageAttendance($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $day     = Carbon::parse($date)->toDateString();
        $payload = $request->input('attendance', []);
        $validPart = $pelatihan->participants;
        $validIdsFlip = $validPart->pluck('id')->flip();
        $participantsById = $validPart->keyBy('id');

        DB::transaction(function () use ($payload, $participantsById, $validIdsFlip, $day, $pelatihan) {
            foreach ($payload as $participantId => $data) {
                if (!$validIdsFlip->has($participantId)) continue;

                $status  = $data['status'] ?? 'absen';
                $note    = $data['note'] ?? null;
                $inTime  = $data['check_in_time']  ?? null;
                $outTime = $data['check_out_time'] ?? null;

                $att = DaftarHadirPelatihan::firstOrNew([
                    'pelatihan_id'   => $pelatihan->id,
                    'participant_id' => $participantId,
                    'date'           => $day,
                ]);

                $att->status = $status;
                $att->note   = $note;

                if ($status === 'hadir') {
                    $att->check_in_time  = $inTime ? $this->normalizeTime($inTime) : $att->check_in_time;
                    $att->check_out_time = $outTime ? $this->normalizeTime($outTime) : $att->check_out_time;
                } else {
                    $att->check_in_time       = null;
                    $att->check_in_timestamp  = null;
                    $att->check_in_photo      = null;
                    $att->check_out_time      = null;
                    $att->check_out_timestamp = null;
                    $att->check_out_photo     = null;
                }
                $att->save();
            }
        });

        return redirect()
            ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
            ->with('success', "Daftar hadir untuk {$day} disimpan.");
    }

    /* ===============================================================
     |  MARK COMPLETE – lock/submit a day
     * ===============================================================*/
    public function markComplete(Request $request, $pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::findOrFail($pelatihanId);

        if (!$this->userCanManageAttendance($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $day = Carbon::parse($date)->toDateString();

        DaftarHadirPelatihanStatus::updateOrCreate(
            ['pelatihan_id' => $pelatihan->id, 'date' => $day],
            [
                'is_submitted' => true,
                'submitted_at' => now(),
                'submitted_by' => $user->id,
                'note'         => $request->input('note'),
            ]
        );

        return redirect()
            ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
            ->with('success', "Hari {$day} ditandai selesai.");
    }

    /* ===============================================================
     |  EXPORT – CSV per day
     * ===============================================================*/
    public function export($pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::with('participants.user')->findOrFail($pelatihanId);

        if (!$this->userCanAccessPelatihan($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $day = Carbon::parse($date)->toDateString();

        $attRows = DaftarHadirPelatihan::where('pelatihan_id', $pelatihan->id)
            ->whereDate('date', $day)
            ->get()
            ->keyBy('participant_id');

        $rows = [];
        foreach ($pelatihan->participants as $p) {
            $att = $attRows->get($p->id);
            $rows[] = [
                'kode_pelatihan'      => $pelatihan->kode_pelatihan,
                'date'                => $day,
                'registration_id'     => $p->registration_id,
                'participant_name'    => $p->user?->name,
                'status'              => $att?->status ?? 'absen',
                'check_in_time'       => $att?->check_in_time,
                'check_in_timestamp'  => optional($att?->check_in_timestamp)->format('Y-m-d H:i:s'),
                'check_in_photo'      => $att?->check_in_photo,
                'check_out_time'      => $att?->check_out_time,
                'check_out_timestamp' => optional($att?->check_out_timestamp)->format('Y-m-d H:i:s'),
                'check_out_photo'     => $att?->check_out_photo,
                'note'                => $att?->note,
            ];
        }

        $filename = 'DaftarHadir_' . $pelatihan->kode_pelatihan . '_' . $day . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, array_keys($rows[0] ?? [
                'kode_pelatihan'      => '',
                'date'                => '',
                'registration_id'     => '',
                'participant_name'    => '',
                'status'              => '',
                'check_in_time'       => '',
                'check_in_timestamp'  => '',
                'check_in_photo'      => '',
                'check_out_time'      => '',
                'check_out_timestamp' => '',
                'check_out_photo'     => '',
                'note'                => '',
            ]));
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ===============================================================
     |  INTERNAL HELPERS
     * ===============================================================*/

    protected function userCanSeeAll($user): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'department_admin' && optional($user->department)->name === 'Human Capital');
    }

    protected function userCanAccessPelatihan($user, $pelatihan): bool
    {
        if ($this->userCanSeeAll($user)) return true;
        return $pelatihan->participants->where('user_id', $user->id)->isNotEmpty();
    }

    protected function userCanManageAttendance($user, $pelatihan): bool
    {
        return $this->userCanSeeAll($user);
    }

    protected function isSuratTugasAccepted(SuratPengajuanPelatihan $pelatihan): bool
    {
        $st = $pelatihan->suratTugas;
        return $st && (int) $st->is_accepted === 1;
    }

    protected function ensureDayStatusRows($pelatihan): void
    {
        if (!$pelatihan->tanggal_mulai || !$pelatihan->durasi) return;
        $start = Carbon::parse($pelatihan->tanggal_mulai);

        for ($i = 0; $i < (int) $pelatihan->durasi; $i++) {
            DaftarHadirPelatihanStatus::firstOrCreate([
                'pelatihan_id' => $pelatihan->id,
                'date'         => $start->copy()->addDays($i)->toDateString(),
            ]);
        }
    }

    protected function buildImportPreviewArray(
        array $rows,
        SuratPengajuanPelatihan $pelatihan,
        string $day,
        $participantsByReg
    ): array {
        $imported = 0;
        $skipped  = 0;
        $preview  = [];

        foreach ($rows as $r) {
            $kodeFile = trim($r['kode_pelatihan'] ?? '');
            if ($kodeFile !== '' && strcasecmp($kodeFile, $pelatihan->kode_pelatihan) !== 0) {
                $skipped++;
                continue;
            }

            $reg = trim($r['registration_id'] ?? '');
            if ($reg === '') {
                $skipped++;
                continue;
            }

            $tsRaw = $r['timestamp'] ?? null;
            if (!$tsRaw) {
                $skipped++;
                continue;
            }
            try {
                $ts = Carbon::parse($tsRaw);
            } catch (\Throwable $e) {
                $skipped++;
                continue;
            }

            if ($ts->toDateString() !== $day) {
                continue;
            }

            $participant = $participantsByReg[$reg] ?? null;
            if (!$participant) {
                $skipped++;
                continue;
            }

            $actRaw     = strtolower(trim($r['presensi'] ?? ''));
            $isCheckOut = $this->isCheckOutString($actRaw);
            $isCheckIn  = !$isCheckOut;

            $inptTime = $r['waktu'] ?? null;
            $normTime = $this->normalizeTime($inptTime) ?? $ts->format('H:i:s');
            $photo    = $r['photo'] ?? null;

            if (!isset($preview[$reg])) {
                $preview[$reg] = [
                    'status'              => 'hadir',
                    'check_in_time'       => null,
                    'check_in_timestamp'  => null,
                    'check_in_photo'      => null,
                    'check_out_time'      => null,
                    'check_out_timestamp' => null,
                    'check_out_photo'     => null,
                ];
            }

            if ($isCheckIn) {
                if (
                    empty($preview[$reg]['check_in_timestamp']) ||
                    $ts->lt(Carbon::parse($preview[$reg]['check_in_timestamp']))
                ) {
                    $preview[$reg]['check_in_timestamp'] = $ts->toDateTimeString();
                    $preview[$reg]['check_in_time']      = $normTime;
                    if ($photo) $preview[$reg]['check_in_photo'] = $photo;
                }
            } else {
                if (
                    empty($preview[$reg]['check_out_timestamp']) ||
                    $ts->gt(Carbon::parse($preview[$reg]['check_out_timestamp']))
                ) {
                    $preview[$reg]['check_out_timestamp'] = $ts->toDateTimeString();
                    $preview[$reg]['check_out_time']      = $normTime;
                    if ($photo) $preview[$reg]['check_out_photo'] = $photo;
                }
            }

            $imported++;
        }

        return [$imported, $skipped, $preview];
    }

    protected function parseAttendanceFile($file): array
    {
        $rows = [];
        if (!$file || !$file->isValid()) return $rows;

        $fh = fopen($file->getRealPath(), 'r');
        $header = null;
        while (($data = fgetcsv($fh, 1000, ',')) !== false) {
            if (!$header) {
                $header = array_map('trim', $data);
                continue;
            }
            $row = [];
            foreach ($header as $idx => $col) {
                $row[strtolower($col)] = $data[$idx] ?? null;
            }
            $rows[] = $row;
        }
        fclose($fh);
        return $rows;
    }

    protected function isCheckOutString(string $s): bool
    {
        return str_contains($s, 'out') || str_contains($s, 'pulang');
    }

    protected function normalizeTime($val): ?string
    {
        if (!$val) return null;
        $val = trim($val);
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $val)) {
            return strlen($val) === 5 ? ($val . ':00') : $val;
        }
        try {
            return Carbon::parse($val)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
