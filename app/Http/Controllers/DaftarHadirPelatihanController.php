<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuanPelatihan;
use App\Models\DaftarHadirPelatihan;
use App\Models\DaftarHadirPelatihanStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class DaftarHadirPelatihanController extends Controller
{
    /* ===============================================================
     | INDEX – list pelatihans eligible for attendance
     * ===============================================================*/
    public function index(Request $request)
    {
        $user = Auth::user();

        $q = SuratPengajuanPelatihan::query()
            ->with(['suratTugas', 'participants.user'])
            ->whereHas('suratTugas', fn($st) => $st->where('is_accepted', 1));

        // Non-admins only see pelatihans they participate in.
        if (!$this->userCanSeeAll($user)) {
            $q->whereHas('participants', fn($p) => $p->where('user_id', $user->id));
        }

        // Search (kode / judul)
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
     | SHOW – training-level page (list all days, presenter per day)
     * ===============================================================*/
    public function show($pelatihanId)
    {
        $user = Auth::user();

        $pelatihan = SuratPengajuanPelatihan::with(['suratTugas', 'daftarHadirStatus'])
            ->findOrFail($pelatihanId);

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
     | DAY – main attendance editor (locked if finalized)
     * ===============================================================*/
    public function day($pelatihanId, $date)
    {
        $user = Auth::user();

        $pelatihan = SuratPengajuanPelatihan::with(['suratTugas', 'participants.user'])
            ->findOrFail($pelatihanId);

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
     | IMPORT – CSV batch update with DATE VALIDATION
     * ===============================================================*/
    public function import(Request $request, $pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::with('participants.user')->findOrFail($pelatihanId);

        if (!$this->isSuratTugasAccepted($pelatihan)) {
            return back()->with('error', 'Surat Tugas belum disetujui; tidak dapat impor daftar hadir.');
        }

        if (!$this->userCanManageAttendance($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $day = Carbon::parse($date)->toDateString();

        $status = DaftarHadirPelatihanStatus::firstOrCreate([
            'pelatihan_id' => $pelatihan->id,
            'date'         => $day,
        ]);

        if ($status->is_submitted) {
            return back()->with('error', 'Hari ini sudah disubmit; import dinonaktifkan.');
        }

        $participantsByReg = $pelatihan->participants
            ->filter(fn($p) => $p->registration_id)
            ->mapWithKeys(fn($p) => [trim($p->registration_id) => $p]);

        [$rows, $mode] = $this->parseAttendanceFile($request->file('file'));

        // 🔥 VALIDASI TANGGAL - Periksa apakah tanggal di Excel sesuai dengan hari pelatihan yang diharapkan
        foreach ($rows as $r) {
            if (!empty($r['timestamp'])) {
                $ts = $this->parseGoogleTimestamp($r['timestamp']);
                if ($ts && $ts->toDateString() !== $day) {
                    return back()->with('error', 
                        "KESALAHAN TANGGAL IMPOR DATA\n\n" .
                        "Tanggal di Excel: {$ts->format('d/m/Y')}\n" .
                        "Tanggal yang diharapkan: " . Carbon::parse($day)->format('d/m/Y') . "\n\n" .
                        "Silakan periksa kembali file Excel Anda atau pastikan mengimpor pada hari pelatihan yang sesuai.\n\n" .
                        "Import dibatalkan untuk mencegah kesalahan data."
                    );
                }
            }
        }

        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, $mode, $participantsByReg, $pelatihan, $day, &$updated, &$skipped) {
            foreach ($rows as $r) {
                $reg = trim($r['registration_id'] ?? '');
                if ($reg === '' || !isset($participantsByReg[$reg])) {
                    $skipped++;
                    continue;
                }

                $participant = $participantsByReg[$reg];
                $att = DaftarHadirPelatihan::firstOrNew([
                    'pelatihan_id' => $pelatihan->id,
                    'user_id'      => $participant->user_id,
                    'date'         => $day,
                ]);

                $att->registration_id = $participant->registration_id;
                $att->kode_pelatihan  = $pelatihan->kode_pelatihan; // ✅ FIX

                if ($mode === 'event') {
                    $checkType  = trim((string)($r['check_type'] ?? ''));
                    $isCheckIn  = strcasecmp($checkType, 'Check In') === 0;
                    $isCheckOut = strcasecmp($checkType, 'Check Out') === 0;

                    $ts  = $this->parseGoogleTimestamp($r['timestamp'] ?? null);
                    $tim = $this->normalizeTime($r['waktu'] ?? null) ?? ($ts ? $ts->format('H:i:s') : null);
                    $ph  = $r['photo'] ?? null;
                    $nt  = $r['note'] ?? null;

                    if ($isCheckIn) {
                        if ($tim) $att->check_in_time       = $tim;
                        if ($ts)  $att->check_in_timestamp  = $ts;
                        if ($ph)  $att->check_in_photo      = $ph;
                    } elseif ($isCheckOut) {
                        if ($tim) $att->check_out_time      = $tim;
                        if ($ts)  $att->check_out_timestamp = $ts;
                        if ($ph)  $att->check_out_photo     = $ph;
                    }

                    if ($nt !== null && $nt !== '') {
                        $att->note = $nt;
                    }

                    $att->status = 'hadir';

                } else {
                    if (!empty($r['status'])) {
                        $att->status = $this->normalizeStatus($r['status']);
                    }
                    if (!empty($r['check_in_time'])) {
                        $att->check_in_time = $this->normalizeTime($r['check_in_time']);
                    }
                    if (!empty($r['check_out_time'])) {
                        $att->check_out_time = $this->normalizeTime($r['check_out_time']);
                    }
                    if (!empty($r['note'])) {
                        $att->note = $r['note'];
                    }
                    if (!empty($r['check_in_photo'])) {
                        $att->check_in_photo = $r['check_in_photo'];
                    }
                    if (!empty($r['check_out_photo'])) {
                        $att->check_out_photo = $r['check_out_photo'];
                    }
                }

                if (!$att->status) {
                    $att->status = 'absen';
                }

                $att->save();
                $updated++;
            }
        });

        return redirect()
            ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
            ->with('success', "Import selesai. Diupdate: {$updated}, Dilewati: {$skipped}.");
    }

    /* ===============================================================
    | SAVE – manual edits; optional finalize via action=finalize
    * ===============================================================*/
    public function save(Request $request, $pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::with([
            'participants.user.department',
            'participants.user.jabatan',
            'participants.department',
            'participants.jabatan',
        ])->findOrFail($pelatihanId);

        if (!$this->userCanManageAttendance($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $day = Carbon::parse($date)->toDateString();

        $status = DaftarHadirPelatihanStatus::firstOrCreate([
            'pelatihan_id' => $pelatihan->id,
            'date'         => $day,
        ]);

        if ($status->is_submitted) {
            return redirect()
                ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
                ->with('error', 'Hari ini sudah disubmit; perubahan diblokir.');
        }

        $payload = $request->input('attendance', []);
        $participantsByUser = $pelatihan->participants->keyBy('user_id');
        $count = 0;

        DB::transaction(function () use ($payload, $participantsByUser, $pelatihan, $day, &$count) {
            foreach ($payload as $userId => $data) {
                if (!$participantsByUser->has($userId)) continue;
                $participant = $participantsByUser[$userId];

                $att = DaftarHadirPelatihan::firstOrNew([
                    'pelatihan_id' => $pelatihan->id,
                    'user_id'      => $userId,
                    'date'         => $day,
                ]);

                $att->registration_id = $participant->registration_id;
                $att->kode_pelatihan  = $pelatihan->kode_pelatihan; // ✅ FIX

                $status  = $this->normalizeStatus($data['status'] ?? 'absen');
                $note    = $data['note'] ?? null;
                $inTime  = $data['check_in_time']  ?? null;
                $outTime = $data['check_out_time'] ?? null;

                $att->status = $status;
                $att->note   = $note;

                if ($status === 'hadir') {
                    $att->check_in_time  = $inTime  ? $this->normalizeTime($inTime)  : null;
                    $att->check_out_time = $outTime ? $this->normalizeTime($outTime) : null;
                } else {
                    $att->check_in_time       = null;
                    $att->check_in_timestamp  = null;
                    $att->check_in_photo      = null;
                    $att->check_out_time      = null;
                    $att->check_out_timestamp = null;
                    $att->check_out_photo     = null;
                }

                $att->save();
                $count++;
            }
        });

        // Finalize logic with superior_id
        if ($request->input('action') === 'finalize') {
            $status->is_submitted = true;
            $status->submitted_at = now();
            $status->submitted_by = $user->id;
            $status->save();

            $allSubmitted = DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihan->id)
                ->where('is_submitted', false)
                ->count() === 0;

            if ($allSubmitted) {
                foreach ($pelatihan->participants as $participant) {
                    $userId = $participant->user_id;

                    $alreadyExists = \App\Models\EvaluasiLevel1::where('user_id', $userId)
                        ->where('pelatihan_id', $pelatihan->id)
                        ->exists();

                    if (!$alreadyExists) {
                        \App\Models\EvaluasiLevel1::create([
                            'pelatihan_id'    => $pelatihan->id,
                            'user_id'         => $userId,
                            'registration_id' => $participant->registration_id,
                            'kode_pelatihan'  => $participant->kode_pelatihan ?? $pelatihan->kode_pelatihan,
                            'superior_id'     => $participant->user->superior_id ?? null, // ✅ ADDED
                            'is_submitted'    => false,
                        ]);
                    }
                }
            }

            return redirect()
                ->route('training.daftarhadirpelatihan.show', $pelatihan->id)
                ->with('success', "Data disimpan & hari {$day} ditandai selesai.");
        }

        return redirect()
            ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
            ->with('success', "Disimpan ({$count}) baris.");
    }


    /* ===============================================================
     | MARK COMPLETE – legacy endpoint (JS call; still supported)
     * ===============================================================*/
    public function markComplete(Request $request, $pelatihanId, $date)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::with('participants.user')->findOrFail($pelatihanId); // ✅ UPDATED: Added user relationship

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
            ]
        );

        // ✅ Check if all dates for this pelatihan are submitted
        $totalDates = DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihan->id)->count();
        $submittedDates = DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihan->id)
            ->where('is_submitted', true)
            ->count();

        if ($totalDates > 0 && $totalDates === $submittedDates) {
            // 🔁 Insert evaluation records for each participant
            $participants = $pelatihan->participants;

            foreach ($participants as $participant) {
                \App\Models\EvaluasiLevel1::firstOrCreate(
                    [
                        'pelatihan_id' => $pelatihan->id,
                        'user_id'      => $participant->user_id,
                    ],
                    [
                        'registration_id' => $participant->registration_id,
                        'kode_pelatihan'  => $participant->kode_pelatihan ?? $pelatihan->kode_pelatihan,
                        'superior_id'     => $participant->user->superior_id ?? null, // ✅ ADDED
                        'is_submitted'    => false,
                    ]
                );
            }
        }

        return redirect()
            ->route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day])
            ->with('success', "Hari {$day} ditandai selesai.");
    }

    /* ===============================================================
     | SET PRESENTER
     * ===============================================================*/
    public function setPresenterDay(Request $request, $pelatihanId, $statusId)
    {
        $user = Auth::user();
        $pelatihan = SuratPengajuanPelatihan::findOrFail($pelatihanId);

        if (!$this->userCanManageAttendance($user, $pelatihan)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $request->validate(['presenter' => 'required|string|max:255']);

        $status = DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihan->id)
            ->where('id', $statusId)
            ->firstOrFail();

        if ($status->is_submitted) {
            return back()->with('error', 'Tidak dapat mengubah presenter: hari sudah disubmit.');
        }

        $status->presenter = $request->presenter;
        $status->save();

        return back()->with('success', 'Presenter berhasil disimpan.');
    }

    /* ===============================================================
     | EXPORT – CSV per day
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
            ->keyBy('user_id');

        $rows = [];
        foreach ($pelatihan->participants as $p) {
            $att = $attRows->get($p->user_id);
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
            fputcsv($out, array_keys($rows[0] ?? []));
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ===============================================================
    | PREVIEW AND PDF
    * ===============================================================*/

    public function preview($pelatihanId, $date)
    {
        $day = \Carbon\Carbon::parse($date)->toDateString();

        $pelatihan = SuratPengajuanPelatihan::with([
            'participants' => function ($q) {
                $q->with([
                    'user',
                    'jabatan',
                    'department',
                    'division',
                    'directorate',
                    'superior',
                ]);
            },
            'presenters' => function ($q) use ($day) {
                $q->whereDate('date', $day)
                ->with(['user', 'presenter']);
            },
        ])->findOrFail($pelatihanId);

        $attendances = \App\Models\DaftarHadirPelatihan::where('pelatihan_id', $pelatihanId)
            ->whereDate('date', $day)
            ->get()
            ->keyBy('user_id');

        $status = \App\Models\DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihanId)
            ->where('date', $day)
            ->first();

        return view('pages.training.daftarhadirpelatihan.preview', compact(
            'pelatihan', 'attendances', 'status', 'day'
        ));
    }

    public function pdf($pelatihanId, $date)
    {
        $day = \Carbon\Carbon::parse($date)->toDateString();

        $pelatihan = SuratPengajuanPelatihan::with([
            'participants' => function ($q) {
                $q->with([
                    'user',
                    'jabatan',
                    'department',
                    'division',
                    'directorate',
                    'superior',
                ]);
            },
            'presenters' => function ($q) use ($day) {
                $q->whereDate('date', $day)
                ->with(['user', 'presenter']);
            },
        ])->findOrFail($pelatihanId);

        $attendances = \App\Models\DaftarHadirPelatihan::where('pelatihan_id', $pelatihanId)
            ->whereDate('date', $day)
            ->get()
            ->keyBy('user_id');

        $status = \App\Models\DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihanId)
            ->where('date', $day)
            ->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pages.training.daftarhadirpelatihan.pdf_view',
            compact('pelatihan', 'attendances', 'status', 'day')
        );

        return $pdf->download('DaftarHadir_' . $pelatihan->kode_pelatihan . '_' . $day . '.pdf');
    }

    /* ===============================================================
     | INTERNAL HELPERS
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
            ], [
                'kode_pelatihan' => $pelatihan->kode_pelatihan,
            ]);
        }
    }

    protected function parseAttendanceFile($file): array
    {
        $rows = [];
        $mode = 'row'; 
        if (!$file || !$file->isValid()) return [$rows, $mode];

        $fh = fopen($file->getRealPath(), 'r');
        $header = null;
        $map = [];

        while (($data = fgetcsv($fh, 2048, ',')) !== false) {
            if (!$header) {
                $header = array_map('trim', $data);

                foreach ($header as $idx => $col) {
                    $lc = strtolower(trim($col));

                    if (str_contains($lc, 'presensi')) $mode = 'event';

                    if (preg_match('/timestamp/i', $lc))       $map['timestamp']        = $idx;
                    if (preg_match('/kode/i', $lc))            $map['kode_pelatihan']   = $idx;
                    if (preg_match('/registrasi|nik|reg id/i', $lc)) 
                                                              $map['registration_id']  = $idx;
                    if (preg_match('/presensi/i', $lc))        $map['check_type']       = $idx;
                    if (preg_match('/waktu/i', $lc))           $map['waktu']            = $idx;
                    if (preg_match('/foto|photo/i', $lc))      $map['photo']            = $idx;
                    if (preg_match('/note|catatan/i', $lc))    $map['note']             = $idx;
                    if (preg_match('/status/i', $lc))          $map['status']           = $idx;
                    if (preg_match('/check in/i', $lc))        $map['check_in_time']    = $idx;
                    if (preg_match('/check out/i', $lc))       $map['check_out_time']   = $idx;
                    if (preg_match('/in photo/i', $lc))        $map['check_in_photo']   = $idx;
                    if (preg_match('/out photo/i', $lc))       $map['check_out_photo']  = $idx;
                }
                continue;
            }

            $row = [
                'timestamp'        => isset($map['timestamp'])        ? ($data[$map['timestamp']] ?? null)        : null,
                'kode_pelatihan'   => isset($map['kode_pelatihan'])   ? ($data[$map['kode_pelatihan']] ?? null)   : null,
                'registration_id'  => isset($map['registration_id'])  ? trim($data[$map['registration_id']] ?? ''): null,
                'check_type'       => isset($map['check_type'])       ? ($data[$map['check_type']] ?? null)       : null,
                'waktu'            => isset($map['waktu'])            ? ($data[$map['waktu']] ?? null)            : null,
                'photo'            => isset($map['photo'])            ? ($data[$map['photo']] ?? null)            : null,
                'note'             => isset($map['note'])             ? ($data[$map['note']] ?? null)             : null,
                'status'           => isset($map['status'])           ? ($data[$map['status']] ?? null)           : null,
                'check_in_time'    => isset($map['check_in_time'])    ? ($data[$map['check_in_time']] ?? null)    : null,
                'check_out_time'   => isset($map['check_out_time'])   ? ($data[$map['check_out_time']] ?? null)   : null,
                'check_in_photo'   => isset($map['check_in_photo'])   ? ($data[$map['check_in_photo']] ?? null)   : null,
                'check_out_photo'  => isset($map['check_out_photo'])  ? ($data[$map['check_out_photo']] ?? null)  : null,
            ];

            $rows[] = $row;
        }

        fclose($fh);
        return [$rows, $mode];
    }

    protected function normalizeStatus(?string $status): string
    {
        $s = strtolower(trim($status ?? ''));
        return in_array($s, ['hadir','izin','sakit','absen']) ? $s : 'absen';
    }

    protected function normalizeTime($time): ?string
    {
        if ($time === null) return null;
        $time = trim((string)$time);
        if ($time === '') return null;

        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            [$h,$m] = explode(':', $time);
            return sprintf('%02d:%02d:00', $h, $m);
        }

        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time)) {
            [$h,$m,$s] = explode(':', $time);
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }

        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function parseGoogleTimestamp($str): ?Carbon
    {
        if ($str === null) return null;
        $str = trim((string)$str);
        if ($str === '') return null;

        $formats = [
            'n/j/Y H:i',
            'n/j/Y H:i:s',
            'm/d/Y H:i',
            'm/d/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y H:i:s',
            'Y-m-d H:i',
            'Y-m-d H:i:s',
            'Y/m/d H:i',
            'Y/m/d H:i:s',
        ];

        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $str);
            } catch (\Throwable $e) {
                // try next
            }
        }

        try {
            return Carbon::parse($str);
        } catch (\Throwable $e) {
            return null;
        }
    }
}