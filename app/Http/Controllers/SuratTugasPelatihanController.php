<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuanPelatihan;
use App\Models\SuratTugasPelatihan;
use App\Models\SuratTugasPelatihanSignatureAndParaf;
use App\Models\SignatureAndParaf;
use App\Models\User;
use App\Models\DaftarHadirPelatihanStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class SuratTugasPelatihanController extends Controller
{
    /* -----------------------------------------------------------------
     |  ACCESS HELPERS
     | -----------------------------------------------------------------*/

    protected function userCanAccessListArea(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        $sigUsers = (array) config('training.signature_users', []);
        if (in_array($user->id, $sigUsers, true)) {
            return true;
        }

        if (
            $user->role === 'department_admin' &&
            optional($user->department)->name === 'Human Capital'
        ) {
            return true;
        }

        return false;
    }

    protected function userCanViewSurat(User $user, SuratTugasPelatihan $surat): bool
    {
        if ($this->userCanAccessListArea($user)) {
            return true;
        }

        if ($surat->created_by && $surat->created_by == $user->id) {
            return true;
        }

        if (!$surat->relationLoaded('signaturesAndParafs')) {
            $surat->load('signaturesAndParafs');
        }
        if ($surat->signaturesAndParafs->where('user_id', $user->id)->isNotEmpty()) {
            return true;
        }

        if (!$surat->relationLoaded('pelatihan.participants')) {
            $surat->load('pelatihan.participants');
        }
        if (
            $surat->pelatihan &&
            $surat->pelatihan->participants->where('user_id', $user->id)->isNotEmpty()
        ) {
            return true;
        }

        return false;
    }

    /* -----------------------------------------------------------------
     |  INDEX
     | -----------------------------------------------------------------*/

    public function index(Request $request): View
    {
        $user = Auth::user();

        // case 1: surat tugas already exists
        $tugasQuery = SuratTugasPelatihan::query()
            ->with([
                'creator',
                'pelatihan.participants.user',
                'signaturesAndParafs.user',
            ]);

        // case 2: belum ada surat tugas, tapi pengajuan sudah diterima
        $pengajuanQuery = SuratPengajuanPelatihan::query()
            ->with([
                'creator',
                'participants.user',
                'signatures.user',
            ])
            ->where('is_accepted', 1)
            ->whereDoesntHave('suratTugas'); // belum dibuatkan surat tugas

        if (!$this->userCanAccessListArea($user)) {
            $tugasQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                ->orWhereHas('signaturesAndParafs', fn($sq) => $sq->where('user_id', $user->id))
                ->orWhereHas('pelatihan.participants', fn($pq) => $pq->where('user_id', $user->id));
            });

            $pengajuanQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                ->orWhereHas('signatures', fn($sq) => $sq->where('user_id', $user->id))
                ->orWhereHas('participants', fn($pq) => $pq->where('user_id', $user->id));
            });
        }

        if ($search = trim($request->q ?? '')) {
            $tugasQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                ->orWhere('kode_pelatihan', 'like', "%{$search}%");
            });

            $pengajuanQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                ->orWhere('kode_pelatihan', 'like', "%{$search}%");
            });
        }

        $suratTugasList = $tugasQuery->latest()->get();
        $suratPengajuanList = $pengajuanQuery->latest()->get();

        // gabung dua koleksi
        $allSurat = $suratTugasList->merge($suratPengajuanList);

        $users = User::orderBy('name')->get();

        return view('pages.training.surattugas.index', [
            'suratList' => $allSurat,
            'users' => $users,
        ]);
    }

    /* -----------------------------------------------------------------
     |  PREVIEW
     | -----------------------------------------------------------------*/

    public function preview($id): View|RedirectResponse
    {
        $surat = SuratTugasPelatihan::with([
            'creator',
            'pelatihan.participants.user',
            'pelatihan.participants.jabatan',
            'pelatihan.participants.department',
            'signaturesAndParafs.user.jabatan',
        ])->findOrFail($id);

        $user = Auth::user();
        if (!$this->userCanViewSurat($user, $surat)) {
            return $this->denyAccessRedirect();
        }

        $regIds = collect()
            ->merge($surat->signaturesAndParafs->pluck('user.registration_id')->filter());

        if ($surat->pelatihan && $surat->pelatihan->participants) {
            $regIds = $regIds->merge(
                $surat->pelatihan->participants->pluck('user.registration_id')->filter()
            );
        }

        $signatureParafMap = SignatureAndParaf::whereIn('registration_id', $regIds->unique()->values())
            ->get()
            ->keyBy('registration_id');

        return view('pages.training.surattugas.preview', compact('surat', 'signatureParafMap'));
    }

    /* -----------------------------------------------------------------
     |  ASSIGN
     | -----------------------------------------------------------------*/

    public function assignView($id): View|RedirectResponse
    {
        // First check if the ID is for a SuratTugas (for reassignment)
        $suratTugas = SuratTugasPelatihan::with(['pelatihan', 'signaturesAndParafs.user'])->find($id);
        
        // If not found as SuratTugas, try to find as SuratPengajuan (for new assignment)
        if (!$suratTugas) {
            $suratPengajuan = SuratPengajuanPelatihan::findOrFail($id);
        } else {
            $suratPengajuan = $suratTugas->pelatihan;
        }

        $user = Auth::user();
        if (!$this->userCanAssign($user)) {
            return $this->denyAccessRedirect();
        }

        // Get latest rejection reason, if any
        $latestRejection = $suratTugas?->signaturesAndParafs()
            ->where('status', 'rejected')
            ->latest('updated_at')
            ->first();

        $users = $this->getAssignableUsers();

        // FIXED CODE HERE - properly get the latest round data
        $existingSuratTugas = $suratTugas;
        $existingParafs = [];
        $existingSignature = null;
        
        if ($suratTugas) {
            // Explicitly load relationships to avoid issues
            $suratTugas->load(['signaturesAndParafs.user', 'signaturesAndParafs.user.jabatan']);
            
            // Get the latest round
            $latestRound = $suratTugas->signaturesAndParafs()->max('round') ?? 1;
            
            // Log detailed debugging info
            \Log::info("AssignView - SuratTugas #{$suratTugas->id}, Pengajuan #{$suratPengajuan->id}, Latest Round: {$latestRound}");
            
            // Get parafs for the latest round
            $parafRecords = $suratTugas->signaturesAndParafs()
                ->where('type', 'paraf')
                ->where('round', $latestRound)
                ->get();
                
            \Log::info("Found {$parafRecords->count()} paraf records in round {$latestRound}");
            
            foreach ($parafRecords as $p) {
                if ($p->user) {
                    $existingParafs[] = [
                        'id' => $p->user_id,
                        'name' => $p->user->name,
                        'registration_id' => $p->user->registration_id,
                        'jabatan_full' => $p->user->jabatan_full ?? ($p->user->jabatan->name ?? '-'),
                    ];
                } else {
                    \Log::warning("Paraf has invalid user_id: {$p->user_id}");
                }
            }
            
            // Get signature for the latest round
            $signatureRecord = $suratTugas->signaturesAndParafs()
                ->where('type', 'signature')
                ->where('round', $latestRound)
                ->first();
            
            if ($signatureRecord && $signatureRecord->user) {
                $existingSignature = [
                    'id' => $signatureRecord->user_id,
                    'name' => $signatureRecord->user->name,
                    'registration_id' => $signatureRecord->user->registration_id, 
                    'jabatan_full' => $signatureRecord->user->jabatan_full ?? ($signatureRecord->user->jabatan->name ?? '-'),
                ];
                
                \Log::info("Signature user: " . json_encode($existingSignature));
            } else if ($signatureRecord) {
                \Log::warning("Signature has invalid user_id: {$signatureRecord->user_id}");
            }
            
            // Debug the tanggal pelaksanaan
            if ($suratTugas->tanggal_pelaksanaan) {
                \Log::info("Tanggal pelaksanaan: " . $suratTugas->tanggal_pelaksanaan);
            }
        }

        return view('pages.training.surattugas.assign', compact(
            'suratPengajuan',
            'existingSuratTugas',
            'existingParafs',
            'existingSignature',
            'users',
            'latestRejection'
        ));
    }

    public function assignSave(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'tempat'                => 'required|string|max:255',
            'durasi'                => 'required|string|max:100',
            'paraf_users'           => 'nullable|array|max:3',
            'paraf_users.*'         => 'nullable|exists:users,id',
            'signature_user'        => 'required|exists:users,id',
            'tujuan'                => 'nullable|string',
            'waktu'                 => 'nullable|string',
            'instruksi'             => 'nullable|string',
            'hal_perhatian'         => 'nullable|string',
            'catatan'               => 'nullable|string',
            'tanggal_mulai'         => 'required|date',
            'tanggal_selesai'       => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_pelaksanaan'   => 'nullable|array',
            'tanggal_pelaksanaan.*' => 'nullable|date',
        ]);

        $user = Auth::user();
        if (!$this->userCanAssign($user)) {
            return $this->denyAccessRedirect();
        }

        // Source pelatihan (immutable after tugas is created)
        $suratPengajuan = SuratPengajuanPelatihan::findOrFail($id);

        // Find or create the Surat Tugas
        $surat = SuratTugasPelatihan::firstOrCreate(
            ['pelatihan_id' => $suratPengajuan->id],
            [
                'kode_pelatihan' => $suratPengajuan->kode_pelatihan,
                'judul'          => $suratPengajuan->judul,
                'created_by'     => $user->id,
                'is_accepted'    => false,
            ]
        );

        $parafIds    = array_filter(array_map('intval', $request->input('paraf_users', [])));
        $signatureId = (int) $request->input('signature_user');

        // Ensure no duplication
        $parafIds = array_diff($parafIds, [$signatureId]);

        $allowedIds = $this->getAssignableUsers()->pluck('id')->all();
        if (!in_array($signatureId, $allowedIds, true)) {
            return back()->withErrors(['signature_user' => 'Penandatangan tidak valid.'])->withInput();
        }
        foreach ($parafIds as $pid) {
            if (!in_array($pid, $allowedIds, true)) {
                return back()->withErrors(['paraf_users' => 'Terdapat user paraf yang tidak valid.'])->withInput();
            }
        }

        $allUserIds = array_merge($parafIds, [$signatureId]);
        $usersMap   = User::whereIn('id', $allUserIds)->get()->keyBy('id');

        $kodePelatihan = $surat->kode_pelatihan ?? ('TUGAS-' . $surat->id);

        DB::transaction(function () use ($surat, $parafIds, $signatureId, $usersMap, $kodePelatihan, $request, $user) {
            // Update Surat Tugas info ONLY (not Surat Pengajuan)
            $surat->update([
                'tempat'               => $request->input('tempat'),
                'durasi'               => $request->input('durasi'),
                'tujuan'               => $request->input('tujuan'),
                'waktu'                => $request->input('waktu'),
                'instruksi'            => $request->input('instruksi'),
                'hal_perhatian'        => $request->input('hal_perhatian'),
                'catatan'              => $request->input('catatan'),
                'tanggal_mulai'        => $request->input('tanggal_mulai'),
                'tanggal_selesai'      => $request->input('tanggal_selesai'),
                'tanggal_pelaksanaan'  => json_encode($request->input('tanggal_pelaksanaan', [])),
                'created_by'           => $user->id,
                'is_accepted'          => false,
            ]);

            // Determine next round
            $lastRound = $surat->signaturesAndParafs()->max('round') ?? 0;
            $round = $lastRound + 1;
            $sequence = 1;

            // Insert parafs
            foreach ($parafIds as $userId) {
                $u = $usersMap[$userId] ?? null;
                if (!$u) continue;

                SuratTugasPelatihanSignatureAndParaf::create([
                    'surat_tugas_id'  => $surat->id,
                    'kode_pelatihan'  => $kodePelatihan,
                    'user_id'         => $userId,
                    'registration_id' => $u->registration_id,
                    'jabatan_id'      => $u->jabatan_id,
                    'jabatan_full'    => $u->jabatan_full ?? ($u->jabatan->name ?? null),
                    'department_id'   => $u->department_id,
                    'directorate_id'  => $u->directorate_id,
                    'division_id'     => $u->division_id,
                    'superior_id'     => $u->superior_id,
                    'golongan'        => $u->golongan ?? null,
                    'round'           => $round,
                    'sequence'        => $sequence++,
                    'type'            => 'paraf',
                    'status'          => 'pending',
                ]);
            }

            // Insert signature
            $sigUser = $usersMap[$signatureId] ?? null;
            if ($sigUser) {
                SuratTugasPelatihanSignatureAndParaf::create([
                    'surat_tugas_id'  => $surat->id,
                    'kode_pelatihan'  => $kodePelatihan,
                    'user_id'         => $signatureId,
                    'registration_id' => $sigUser->registration_id,
                    'jabatan_id'      => $sigUser->jabatan_id,
                    'jabatan_full'    => $sigUser->jabatan_full ?? ($sigUser->jabatan->name ?? null),
                    'department_id'   => $sigUser->department_id,
                    'directorate_id'  => $sigUser->directorate_id,
                    'division_id'     => $sigUser->division_id,
                    'superior_id'     => $sigUser->superior_id,
                    'golongan'        => $sigUser->golongan ?? null,
                    'round'           => $round,
                    'sequence'        => $sequence,
                    'type'            => 'signature',
                    'status'          => 'pending',
                ]);
            }
        });

        return redirect()->route('training.surattugas.index')
            ->with('success', 'Penandatangan dan paraf berhasil ditetapkan.');
    }

    protected function userCanAssign(User $user): bool
    {
        $isAdmin = $user->role === 'admin';
        $isHumanCapitalManager =
            optional($user->department)->name === 'Human Capital' &&
            strtolower(optional($user->jabatan)->name) === 'manager';
        $isHcFinanceDirector =
            optional($user->directorate)->name === 'Human Capital & Finance' &&
            strtolower(optional($user->jabatan)->name) === 'director';

        return ($isAdmin || $isHumanCapitalManager || $isHcFinanceDirector);
    }

    /* -----------------------------------------------------------------
     |  APPROVE / REJECT
     | -----------------------------------------------------------------*/

    public function approveView($id, $approvalId): View|RedirectResponse
    {
        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat    = SuratTugasPelatihan::with('pelatihan')->findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            return $this->denyAccessRedirect();
        }

        return view('pages.training.surattugas.approve', compact('surat', 'approval'));
    }

    public function approve(Request $request, $id, $approvalId): RedirectResponse
    {
        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat    = SuratTugasPelatihan::with('pelatihan')->findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            return $this->denyAccessRedirect();
        }

        $approval->update([
            'status'    => 'approved',
            'signed_at' => now(),
        ]);

        $pending = $surat->signaturesAndParafs()->where('status', 'pending')->count();
        if ($pending === 0) {
            $surat->update(['is_accepted' => true]);
            /*
            $pelatihan = $surat->pelatihan;
            if ($pelatihan && $pelatihan->tanggal_mulai && $pelatihan->durasi) {
                $kodePelatihan = $surat->kode_pelatihan
                    ?? $pelatihan->kode_pelatihan
                    ?? ('TUGAS-' . $surat->id);

                $start = Carbon::parse($pelatihan->tanggal_mulai);
                for ($i = 0; $i < (int) $pelatihan->durasi; $i++) {
                    DaftarHadirPelatihanStatus::firstOrCreate([
                        'pelatihan_id' => $pelatihan->id,
                        'date'         => $start->copy()->addDays($i)->toDateString(),
                    ], [
                        'kode_pelatihan' => $kodePelatihan,
                    ]);
                }
            }
            */
        }

        return redirect()
            ->route('training.surattugas.index')
            ->with('success', 'Surat telah disetujui.');
    }

    public function rejectView($id, $approvalId): View|RedirectResponse
    {
        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat    = SuratTugasPelatihan::findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            return $this->denyAccessRedirect();
        }

        return view('pages.training.surattugas.reject', compact('surat', 'approval'));
    }

    public function reject(Request $request, $id, $approvalId): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:255']);

        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat    = SuratTugasPelatihan::findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            return $this->denyAccessRedirect();
        }

        DB::transaction(function () use ($approval, $surat, $request) {
            // Mark current approval as rejected with reason
            $approval->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->reason,
                'signed_at'        => now(),
            ]);

            // Auto reject all later approvals in same round
            SuratTugasPelatihanSignatureAndParaf::where('surat_tugas_id', $surat->id)
                ->where('round', $approval->round)
                ->where('sequence', '>', $approval->sequence)
                ->where('status', 'pending')
                ->update([
                    'status'           => 'rejected',
                    'rejection_reason' => 'Auto rejected due to earlier rejection. Original reason: ' . $request->reason,
                    'signed_at'        => now(),
                ]);

            $surat->update(['is_accepted' => false]);
        });

        return redirect()
            ->route('training.surattugas.index')
            ->with('warning', 'Surat ditolak. Semua approval berikutnya otomatis ditolak dengan alasan bawaan.');
    }

    /* -----------------------------------------------------------------
     |  DOWNLOAD PDF
     | -----------------------------------------------------------------*/

    public function download($id)
    {
        $surat = SuratTugasPelatihan::with([
            'pelatihan.participants.user',
            'pelatihan.participants.jabatan',
            'pelatihan.participants.department',
            'signaturesAndParafs.user.jabatan',
        ])->findOrFail($id);

        $user = Auth::user();
        if (!$this->userCanViewSurat($user, $surat)) {
            return $this->denyAccessRedirect();
        }

        $regIds = collect()
            ->merge($surat->signaturesAndParafs->pluck('user.registration_id')->filter());

        if ($surat->pelatihan && $surat->pelatihan->participants) {
            $regIds = $regIds->merge(
                $surat->pelatihan->participants->pluck('user.registration_id')->filter()
            );
        }

        $signatureParafMap = SignatureAndParaf::whereIn('registration_id', $regIds->unique()->values())
            ->get()
            ->keyBy('registration_id');

        $kode = $surat->kode_pelatihan
            ?? $surat->pelatihan?->kode_pelatihan
            ?? ('TUGAS-' . $surat->id);

        $pdf = Pdf::loadView('pages.training.surattugas.pdf_view', [
            'surat'             => $surat,
            'signatureParafMap' => $signatureParafMap,
        ]);

        return $pdf->download('ST-' . $kode . '.pdf');
    }

    /* -----------------------------------------------------------------
     |  UNAUTHORIZED REDIRECT
     | -----------------------------------------------------------------*/

    protected function denyAccessRedirect()
    {
        $msg = 'Anda tidak diizinkan untuk melihat halaman ini.';

        $prev = url()->previous();
        if ($prev && $prev !== url()->current()) {
            return redirect()->back()->with('error', $msg);
        }

        foreach (['home', 'training.surattugas.index', 'training.suratpengajuan.index', 'user.index'] as $route) {
            if (Route::has($route)) {
                return redirect()->route($route)->with('error', $msg);
            }
        }

        abort(403, $msg);
    }

    /* -----------------------------------------------------------------
     |  ASSIGNABLE USERS SOURCE
     | -----------------------------------------------------------------*/

    protected function getAssignableUsers()
    {
        return User::where(function ($outer) {
                $outer->where(function ($q) {
                    $q->whereHas('directorate', function ($dq) {
                        $dq->where('name', 'Human Capital & Finance');
                    })->whereHas('jabatan', function ($jq) {
                        $jq->whereRaw('LOWER(`name`) = ?', ['director']);
                    });
                })
                ->orWhereHas('department', function ($dq) {
                    $dq->where('name', 'Human Capital');
                });
            })
            ->orderBy('name')
            ->get();
    }
}
