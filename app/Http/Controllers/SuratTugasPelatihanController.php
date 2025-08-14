<?php

namespace App\Http\Controllers;

use App\Models\SuratTugasPelatihan;
use App\Models\SuratTugasPelatihanSignatureAndParaf;
use App\Models\SignatureAndParaf;
use App\Models\User;
use App\Models\DaftarHadirPelatihanStatus; // NEW
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

    /**
     * Global "list area" access: can see ALL Surat Tugas entries.
     *
     * - role = admin
     * - OR user ID in config('training.signature_users')
     * - OR role = department_admin AND department name = "Human Capital"
     */
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

    /**
     * Document-level access: can this user view THIS Surat Tugas?
     *
     * Allowed if:
     * - user has global list access, OR
     * - user is the surat creator, OR
     * - user is assigned as paraf/signature, OR
     * - user is a participant in the related pelatihan.
     */
    protected function userCanViewSurat(User $user, SuratTugasPelatihan $surat): bool
    {
        if ($this->userCanAccessListArea($user)) {
            return true;
        }

        if ($surat->created_by && $surat->created_by == $user->id) {
            return true;
        }

        // ensure relation loaded; if not, lazy-load
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

    /**
     * List Surat Tugas Pelatihan.
     *
     * Global users see all records.
     * Ordinary users see only surat where they are creator, paraf/sign, or participant.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = SuratTugasPelatihan::query()
            ->with(['creator', 'signaturesAndParafs.user', 'pelatihan']);

        // Restrict visibility for ordinary users
        if (!$this->userCanAccessListArea($user)) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('signaturesAndParafs', function ($sq) use ($user) {
                      $sq->where('user_id', $user->id);
                  })
                  ->orWhereHas('pelatihan.participants', function ($pq) use ($user) {
                      $pq->where('user_id', $user->id);
                  });
            });
        }

        // Search by judul or kode pelatihan (local fields or related pelatihan)
        if ($search = trim($request->q ?? '')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_pelatihan', 'like', "%{$search}%")
                  ->orWhereHas('pelatihan', function ($pel) use ($search) {
                      $pel->where('kode_pelatihan', 'like', "%{$search}%")
                          ->orWhere('judul', 'like', "%{$search}%");
                  });
            });
        }

        $suratTugasList = $query->latest()->get();
        $users = User::orderBy('name')->get();

        return view('pages.training.surattugas.index', compact('suratTugasList', 'users'));
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

        // Build map of available signature/paraf images
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
     |  ASSIGN (Privileged Only)
     | -----------------------------------------------------------------*/

    public function assignView($id): View|RedirectResponse
    {
        $suratTugas = SuratTugasPelatihan::with('pelatihan')->findOrFail($id);
        $user = Auth::user();

        if (!$this->userCanAssign($user)) {
            return $this->denyAccessRedirect();
        }

        $users = $this->getAssignableUsers();

        return view('pages.training.surattugas.assign', compact('suratTugas', 'users'));
    }

    public function assignSave(Request $request): RedirectResponse
    {
        $request->validate([
            'surat_tugas_id' => 'required|exists:surat_tugas_pelatihans,id',
            'paraf_users'    => 'nullable|array|max:3',
            'paraf_users.*'  => 'nullable|exists:users,id',
            'signature_user' => 'required|exists:users,id',
            'tujuan'         => 'nullable|string',
            'waktu'          => 'nullable|string',
            'instruksi'      => 'nullable|string',
            'hal_perhatian'  => 'nullable|string',
            'catatan'        => 'nullable|string',
        ]);

        $user = Auth::user();
        if (!$this->userCanAssign($user)) {
            return $this->denyAccessRedirect();
        }

        $surat = SuratTugasPelatihan::with('pelatihan')->findOrFail($request->surat_tugas_id);

        $parafIds    = array_filter(array_map('intval', $request->input('paraf_users', [])));
        $signatureId = (int) $request->input('signature_user');

        // Remove signature user from paraf if included
        $parafIds = array_diff($parafIds, [$signatureId]);

        // Validate IDs
        $allowedIds = $this->getAssignableUsers()->pluck('id')->all();

        if (!in_array($signatureId, $allowedIds, true)) {
            return back()
                ->withErrors(['signature_user' => 'Penandatangan tidak valid.'])
                ->withInput();
        }
        foreach ($parafIds as $pid) {
            if (!in_array($pid, $allowedIds, true)) {
                return back()
                    ->withErrors(['paraf_users' => 'Terdapat user paraf yang tidak valid.'])
                    ->withInput();
            }
        }

        // preload all referenced users (avoid N+1)
        $allUserIds = array_merge($parafIds, [$signatureId]);
        $usersMap   = User::whereIn('id', $allUserIds)->get()->keyBy('id');

        // fallback kode pelatihan
        $kodePelatihan = $surat->kode_pelatihan
            ?? optional($surat->pelatihan)->kode_pelatihan
            ?? ('TUGAS-' . $surat->id);

        DB::transaction(function () use ($surat, $parafIds, $signatureId, $usersMap, $kodePelatihan, $request) {
            // Update surat tugas with additional fields
            $surat->update([
                'tujuan'        => $request->input('tujuan'),
                'waktu'         => $request->input('waktu'),
                'instruksi'     => $request->input('instruksi'),
                'hal_perhatian' => $request->input('hal_perhatian'),
                'catatan'       => $request->input('catatan'),
            ]);

            $surat->signaturesAndParafs()->delete();

            $round    = 1;
            $sequence = 1;

            foreach ($parafIds as $userId) {
                $user = $usersMap[$userId] ?? null;
                if (!$user) {
                    continue;
                }
                SuratTugasPelatihanSignatureAndParaf::create([
                    'surat_tugas_id'  => $surat->id,
                    'kode_pelatihan'  => $kodePelatihan,
                    'user_id'         => $userId,
                    'registration_id' => $user->registration_id,
                    'jabatan_id'      => $user->jabatan_id,
                    'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->name ?? null),
                    'department_id'   => $user->department_id,
                    'directorate_id'  => $user->directorate_id,
                    'division_id'     => $user->division_id,
                    'superior_id'     => $user->superior_id,
                    'golongan'        => $user->golongan ?? null,
                    'round'           => $round,
                    'sequence'        => $sequence++,
                    'type'            => 'paraf',
                    'status'          => 'pending',
                ]);
            }

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

        return redirect()
            ->route('training.surattugas.index')
            ->with('success', 'Penandatangan dan paraf berhasil ditetapkan.');
    }

    /**
     * Helper: who can assign?
     */
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

        // mark approved w/ timestamp
        $approval->update([
            'status'    => 'approved',
            'signed_at' => now(),
        ]);

        // still pending?
        $pending = $surat->signaturesAndParafs()->where('status', 'pending')->count();
        if ($pending === 0) {
            // final accept
            $surat->update(['is_accepted' => true]);

            // auto-create Daftar Hadir Pelatihan Status rows for each day
            $pelatihan = $surat->pelatihan;
            if ($pelatihan && $pelatihan->tanggal_mulai && $pelatihan->durasi) {
                $start = Carbon::parse($pelatihan->tanggal_mulai);
                for ($i = 0; $i < (int) $pelatihan->durasi; $i++) {
                    DaftarHadirPelatihanStatus::firstOrCreate([
                        'pelatihan_id' => $pelatihan->id,
                        'date'         => $start->copy()->addDays($i)->toDateString(),
                    ]);
                }
            }
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

        $approval->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
            'signed_at'        => now(),
        ]);

        return redirect()
            ->route('training.surattugas.index')
            ->with('warning', 'Surat ditolak.');
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

        if (Route::has('home')) {
            return redirect()->route('home')->with('error', $msg);
        }
        if (Route::has('training.surattugas.index')) {
            return redirect()->route('training.surattugas.index')->with('error', $msg);
        }
        if (Route::has('training.suratpengajuan.index')) {
            return redirect()->route('training.suratpengajuan.index')->with('error', $msg);
        }
        if (Route::has('user.index')) {
            return redirect()->route('user.index')->with('error', $msg);
        }

        abort(403, $msg);
    }

    /* -----------------------------------------------------------------
     |  ASSIGNABLE USERS SOURCE
     | -----------------------------------------------------------------*/

    /**
     * Users allowed to be selected as paraf/signature:
     * - Directors in the "Human Capital & Finance" directorate
     * - Anyone in the "Human Capital" department
     */
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
