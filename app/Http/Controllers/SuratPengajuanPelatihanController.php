<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SuratPengajuanPelatihan;
use App\Models\TrainingParticipant;
use App\Models\SuratPengajuanPelatihanSignatureAndParaf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SuratPengajuanPelatihanController extends Controller
{
    /* -----------------------------------------------------------------
     | Index
     |----------------------------------------------------------------- */
    public function index()
    {
        $user = Auth::user();

        $examples = SuratPengajuanPelatihan::with(['approvals.user', 'participants.user']);

        if ($user->role !== 'admin') {
            $examples = $examples->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('participants', fn ($q) => $q->where('user_id', $user->id))
                    ->orWhereHas('approvals', fn ($q) => $q->where('user_id', $user->id));
            });
        }

        $examples = $examples->latest()->get();

        return view('pages.training.suratpengajuan.index', compact('examples'));
    }

    /* -----------------------------------------------------------------
     | Create / Store
     |----------------------------------------------------------------- */
    public function create()
    {
        $users = User::all();
        return view('pages.training.suratpengajuan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelatihan'        => 'required|string|unique:surat_pengajuan_pelatihans,kode_pelatihan',
            'kompetensi'            => 'required|string',
            'judul'                 => 'required|string',
            'lokasi'                => 'required|string',
            'instruktur'            => 'required|string',
            'sifat'                 => 'required|string',
            'kompetensi_wajib'      => 'required|string',
            'materi_global'         => 'required|string',
            'program_pelatihan_ksp' => 'required|string',
            'tanggal_mulai'         => 'required|date',
            'tanggal_selesai'       => 'required|date|after_or_equal:tanggal_mulai',
            'durasi'                => 'required|integer|min:1',
            'tempat'                => 'required|string',
            'penyelenggara'         => 'required|string',
            'biaya'                 => 'required|string',
            'per_paket_or_orang'    => 'required|string',
            'keterangan'            => 'nullable|string',

            'participants'          => 'required|array|min:1',
            'participants.*'        => 'exists:users,registration_id',

            'parafs'                => 'nullable|array|max:3',
            'parafs.*'              => 'exists:users,registration_id',

            'signatures'            => 'required|array|min:1',
            'signatures.*'          => 'exists:users,registration_id',

            'signature2'            => 'nullable|array|max:1',
            'signature2.*'          => 'exists:users,registration_id',

            'signature3'            => 'nullable|array|max:1',
            'signature3.*'          => 'exists:users,registration_id',
        ]);

        DB::beginTransaction();

        try {
            $surat = SuratPengajuanPelatihan::create([
                'created_by'            => auth()->id(),
                'kode_pelatihan'        => $validated['kode_pelatihan'],
                'kompetensi'            => $validated['kompetensi'],
                'judul'                 => $validated['judul'],
                'lokasi'                => $validated['lokasi'],
                'instruktur'            => $validated['instruktur'],
                'sifat'                 => $validated['sifat'],
                'kompetensi_wajib'      => $validated['kompetensi_wajib'],
                'materi_global'         => $validated['materi_global'],
                'program_pelatihan_ksp' => $validated['program_pelatihan_ksp'],
                'tanggal_mulai'         => $validated['tanggal_mulai'],
                'tanggal_selesai'       => $validated['tanggal_selesai'],
                'durasi'                => $validated['durasi'],
                'tempat'                => $validated['tempat'],
                'penyelenggara'         => $validated['penyelenggara'],
                'biaya'                 => $validated['biaya'],
                'per_paket_or_orang'    => $validated['per_paket_or_orang'],
                'keterangan'            => $validated['keterangan'],
            ]);

            // Participants
            foreach ($validated['participants'] as $registrationId) {
                if ($user = User::where('registration_id', $registrationId)->first()) {
                    TrainingParticipant::create([
                        'pelatihan_id'    => $surat->id,
                        'user_id'         => $user->id,
                        'kode_pelatihan'  => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'jabatan_id'      => $user->jabatan_id,
                        'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->nama ?? null),
                        'department_id'   => $user->department_id,
                        'directorate_id'  => $user->directorate_id,
                        'division_id'     => $user->division_id,
                        'superior_id'     => $user->superior_id,
                        'golongan'        => $user->golongan ?? null,
                    ]);
                }
            }

            // Approval chain
            $sequence = 1;
            $round    = 1;

            // Parafs
            foreach ($validated['parafs'] ?? [] as $registrationId) {
                if ($user = User::where('registration_id', $registrationId)->first()) {
                    SuratPengajuanPelatihanSignatureAndParaf::create([
                        'pelatihan_id'    => $surat->id,
                        'user_id'         => $user->id,
                        'kode_pelatihan'  => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'user_name'       => $user->name,
                        'jabatan_id'      => $user->jabatan_id,
                        'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->nama ?? null),
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
            }

            // Signatures (could be up to 3 buckets)
            foreach (['signatures', 'signature2', 'signature3'] as $key) {
                foreach ($validated[$key] ?? [] as $registrationId) {
                    if ($user = User::where('registration_id', $registrationId)->first()) {
                        SuratPengajuanPelatihanSignatureAndParaf::create([
                            'pelatihan_id'    => $surat->id,
                            'user_id'         => $user->id,
                            'kode_pelatihan'  => $surat->kode_pelatihan,
                            'registration_id' => $user->registration_id,
                            'user_name'       => $user->name,
                            'jabatan_id'      => $user->jabatan_id,
                            'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->nama ?? null),
                            'department_id'   => $user->department_id,
                            'directorate_id'  => $user->directorate_id,
                            'division_id'     => $user->division_id,
                            'superior_id'     => $user->superior_id,
                            'golongan'        => $user->golongan ?? null,
                            'round'           => $round,
                            'sequence'        => $sequence++,
                            'type'            => 'signature',
                            'status'          => 'pending',
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()
                ->route('training.suratpengajuan.index')
                ->with('success', 'Surat pengajuan pelatihan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan surat: ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     | Edit
     |----------------------------------------------------------------- */
    public function edit($id)
    {
        $surat = SuratPengajuanPelatihan::with([
            'creator',
            'participants.user',
            'approvals.user',
        ])->findOrFail($id);

        // Check if user is creator
        if ((int)auth()->id() !== (int)$surat->created_by) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah surat ini.');
        }

        // Check if surat has rejection in latest round
        $latestRound = $surat->approvals->max('round');
        $hasRejection = $surat->approvals()
            ->where('round', $latestRound)
            ->where('status', 'rejected')
            ->exists();

        if (!$hasRejection) {
            return redirect()
                ->route('training.suratpengajuan.index')
                ->with('error', 'Surat tidak dapat diubah karena belum ada penolakan pada round terakhir.');
        }

        $latestRejection = $surat->approvals
            ->where('round', $latestRound)
            ->firstWhere('status', 'rejected');

        $users = User::all();

        return view('pages.training.suratpengajuan.edit', compact('surat', 'users', 'latestRejection'));
    }

    /* -----------------------------------------------------------------
     | Preview  (Browser)
     |----------------------------------------------------------------- */
    public function preview($id)
    {
        $surat = SuratPengajuanPelatihan::with([
            'creator',
            'participants.user',
            'approvals.user',
        ])->findOrFail($id);

        // hydrate preview_url & pdf_path on each approval
        $this->hydrateApprovalImages($surat);

        return view('pages.training.suratpengajuan.preview', compact('surat'));
    }

    /* -----------------------------------------------------------------
     | Download PDF  (DomPDF)
     |----------------------------------------------------------------- */
    public function downloadPDF($id)
    {
        $surat = SuratPengajuanPelatihan::with([
            'creator',
            'participants.user',
            'approvals.user',
        ])->findOrFail($id);

        // hydrate preview_url & pdf_path on each approval
        $this->hydrateApprovalImages($surat);

        $pdf = Pdf::loadView('pages.training.suratpengajuan.pdf_view', compact('surat'));

        $filename = Str::slug($surat->judul) . '.pdf';
        return $pdf->download($filename);
    }

    /* -----------------------------------------------------------------
     | Update (after rejection)
     |----------------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kompetensi'            => 'required|string',
            'judul'                 => 'required|string',
            'lokasi'                => 'required|string',
            'instruktur'            => 'required|string',
            'sifat'                 => 'required|string',
            'kompetensi_wajib'      => 'required|string',
            'materi_global'         => 'required|string',
            'program_pelatihan_ksp' => 'required|string',
            'tanggal_mulai'         => 'required|date',
            'tanggal_selesai'       => 'required|date|after_or_equal:tanggal_mulai',
            'durasi'                => 'required|integer|min:1',
            'tempat'                => 'required|string',
            'penyelenggara'         => 'required|string',
            'biaya'                 => 'required|string',
            'per_paket_or_orang'    => 'required|string',
            'keterangan'            => 'nullable|string',

            'participants'          => 'required|array|min:1',
            'participants.*'        => 'exists:users,registration_id',

            'parafs'                => 'nullable|array|max:3',
            'parafs.*'              => 'exists:users,registration_id',

            'signatures'            => 'required|array|min:1',
            'signatures.*'          => 'exists:users,registration_id',

            'signature2'            => 'nullable|array|max:1',
            'signature2.*'          => 'exists:users,registration_id',

            'signature3'            => 'nullable|array|max:1',
            'signature3.*'          => 'exists:users,registration_id',
        ]);

        $surat = SuratPengajuanPelatihan::findOrFail($id);

        if ((int)auth()->id() !== (int)$surat->created_by) {
            abort(403);
        }

        $latestRound = $surat->approvals()->max('round') ?? 1;

        // must have at least one rejection in latest round
        $rejected = $surat->approvals()
            ->where('round', $latestRound)
            ->where('status', 'rejected')
            ->exists();

        if (!$rejected) {
            return back()->with('error', 'Surat tidak dapat diubah karena belum ada penolakan pada round terakhir.');
        }

        DB::beginTransaction();

        try {
            // Update surat
            $surat->update($validated);

            // Replace participants
            $surat->participants()->delete();
            foreach ($validated['participants'] as $registrationId) {
                if ($user = User::where('registration_id', $registrationId)->first()) {
                    TrainingParticipant::create([
                        'pelatihan_id'    => $surat->id,
                        'user_id'         => $user->id,
                        'kode_pelatihan'  => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'jabatan_id'      => $user->jabatan_id,
                        'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->nama ?? null),
                        'department_id'   => $user->department_id,
                        'directorate_id'  => $user->directorate_id,
                        'division_id'     => $user->division_id,
                        'superior_id'     => $user->superior_id,
                        'golongan'        => $user->golongan ?? null,
                    ]);
                }
            }

            // New round
            $nextRound = $latestRound + 1;
            $sequence  = 1;

            // Parafs
            foreach ($validated['parafs'] ?? [] as $registrationId) {
                if ($user = User::where('registration_id', $registrationId)->first()) {
                    SuratPengajuanPelatihanSignatureAndParaf::create([
                        'pelatihan_id'    => $surat->id,
                        'user_id'         => $user->id,
                        'kode_pelatihan'  => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'user_name'       => $user->name,
                        'jabatan_id'      => $user->jabatan_id,
                        'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->nama ?? null),
                        'department_id'   => $user->department_id,
                        'directorate_id'  => $user->directorate_id,
                        'division_id'     => $user->division_id,
                        'superior_id'     => $user->superior_id,
                        'golongan'        => $user->golongan ?? null,
                        'round'           => $nextRound,
                        'sequence'        => $sequence++,
                        'type'            => 'paraf',
                        'status'          => 'pending',
                    ]);
                }
            }

            // Signatures (1..3 buckets)
            foreach (['signatures', 'signature2', 'signature3'] as $key) {
                foreach ($validated[$key] ?? [] as $registrationId) {
                    if ($user = User::where('registration_id', $registrationId)->first()) {
                        SuratPengajuanPelatihanSignatureAndParaf::create([
                            'pelatihan_id'    => $surat->id,
                            'user_id'         => $user->id,
                            'kode_pelatihan'  => $surat->kode_pelatihan,
                            'registration_id' => $user->registration_id,
                            'user_name'       => $user->name,
                            'jabatan_id'      => $user->jabatan_id,
                            'jabatan_full'    => $user->jabatan_full ?? ($user->jabatan->nama ?? null),
                            'department_id'   => $user->department_id,
                            'directorate_id'  => $user->directorate_id,
                            'division_id'     => $user->division_id,
                            'superior_id'     => $user->superior_id,
                            'golongan'        => $user->golongan ?? null,
                            'round'           => $nextRound,
                            'sequence'        => $sequence++,
                            'type'            => 'signature',
                            'status'          => 'pending',
                        ]);
                    }
                }
            }

            // Mark any still-pending approvals from old round as rejected (optional)
            $surat->approvals()
                ->where('round', $latestRound)
                ->where('status', 'pending')
                ->update([
                    'status'           => 'rejected',
                    'rejection_reason' => 'Auto rejected by resubmission.',
                    'signed_at'        => now(),
                ]);

            DB::commit();
            return redirect()
                ->route('training.suratpengajuan.index')
                ->with('success', 'Surat berhasil diperbarui dan diajukan ulang.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan surat: ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     | Approve / Reject
     |----------------------------------------------------------------- */
    public function approve($suratId, $approvalId)
    {
        $approval = SuratPengajuanPelatihanSignatureAndParaf::where('id', $approvalId)
            ->where('pelatihan_id', $suratId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        // enforce sequence
        $minPending = SuratPengajuanPelatihanSignatureAndParaf::where('pelatihan_id', $suratId)
            ->where('round', $approval->round)
            ->where('status', 'pending')
            ->orderBy('sequence')
            ->first();

        if ($minPending->id !== $approval->id) {
            return back()->with('error', 'Belum waktunya Anda menyetujui surat ini.');
        }

        DB::transaction(function () use ($approval, $suratId) {
            $approval->update([
                'status'    => 'approved',
                'signed_at' => now(),
            ]);

            $surat       = SuratPengajuanPelatihan::with('approvals')->findOrFail($suratId);
            $currentRound= $approval->round;

            $allCompleted = $surat->approvals()
                ->where('round', $currentRound)
                ->where('status', 'pending')
                ->doesntExist();

            if ($allCompleted) {
                $alreadyExists = \App\Models\SuratTugasPelatihan::where('pelatihan_id', $surat->id)->exists();

                if (!$alreadyExists) {
                    \App\Models\SuratTugasPelatihan::create([
                        'pelatihan_id'      => $surat->id,
                        'kode_pelatihan'    => $surat->kode_pelatihan,
                        'judul'             => $surat->judul,
                        'tanggal'           => now()->toDateString(),
                        'tempat'            => $surat->tempat,
                        'tanggal_pelatihan' => $surat->tanggal_mulai,
                        'durasi'            => $surat->durasi,
                        'created_by'        => auth()->id(),
                        'status'            => 'draft',
                        'is_accepted'       => false,
                    ]);
                }
            }
        });

        return back()->with('success', 'Surat telah disetujui.');
    }

    public function reject(Request $request, $suratId, $approvalId)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $approval = SuratPengajuanPelatihanSignatureAndParaf::where('id', $approvalId)
            ->where('pelatihan_id', $suratId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        // enforce sequence
        $minPending = SuratPengajuanPelatihanSignatureAndParaf::where('pelatihan_id', $suratId)
            ->where('round', $approval->round)
            ->where('status', 'pending')
            ->orderBy('sequence')
            ->first();

        if ($minPending->id !== $approval->id) {
            return back()->with('error', 'Belum waktunya Anda menolak surat ini.');
        }

        DB::transaction(function () use ($approval, $request) {
            $approval->update([
                'status'           => 'rejected',
                'signed_at'        => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Auto reject later steps in same round
            SuratPengajuanPelatihanSignatureAndParaf::where('pelatihan_id', $approval->pelatihan_id)
                ->where('round', $approval->round)
                ->where('sequence', '>', $approval->sequence)
                ->where('status', 'pending')
                ->update([
                    'status'           => 'rejected',
                    'signed_at'        => now(),
                    'rejection_reason' => 'Auto rejected due to earlier rejection',
                ]);
        });

        return back()->with('danger', 'Surat telah ditolak dan dikembalikan ke pembuat.');
    }

    /* -----------------------------------------------------------------
     | Helpers
     |----------------------------------------------------------------- */

    /**
     * Build preview (URL) + pdf (abs path) from the raw DB path fragment.
     *
     * Accepts things like:
     *   signatures/ADM01.png
     *   parafs/ADM01.png
     *   storage/signatures/ADM01.png
     *   public/storage/signatures/ADM01.png
     *
     * Returns [ $previewUrl, $absPath ].
     */
    protected function normalizeSignaturePaths(?string $raw): array
    {
        if (!$raw) {
            return [null, null];
        }

        $path = str_replace('\\', '/', $raw);
        $path = ltrim($path, '/');

        // strip leading public/
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        // ensure "storage/" prefix (public/storage symlink)
        if (!str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }

        // public URL for browser preview
        $previewUrl = asset($path);

        // absolute filesystem path for DomPDF
        $absPath = public_path($path);

        return [$previewUrl, $absPath];
    }

    /**
     * For every approval in the given SuratPengajuanPelatihan instance,
     * attach $approval->preview_url and $approval->pdf_path
     * (resolving via the signature_and_parafs table if needed).
     */
    protected function hydrateApprovalImages(SuratPengajuanPelatihan $surat): void
    {
        // get all unique registration_ids from approvals
        $regIds = $surat->approvals->pluck('registration_id')->filter()->unique()->values();

        // load signatures once
        $sigRecords = DB::table('signature_and_parafs')
            ->whereIn('registration_id', $regIds)
            ->get()
            ->keyBy('registration_id');

        foreach ($surat->approvals as $approval) {
            $raw = null;

            if ($approval->image_path) {
                // legacy field already set (maybe by older code)
                $raw = $approval->image_path;
            } else {
                // fallback from signature_and_parafs table
                $sigRecord = $sigRecords[$approval->registration_id] ?? null;
                if ($sigRecord) {
                    if ($approval->type === 'paraf') {
                        $raw = $sigRecord->paraf_path;
                    } else { // signature
                        $raw = $sigRecord->signature_path;
                    }
                }
            }

            [$previewUrl, $absPath] = $this->normalizeSignaturePaths($raw);
            $approval->preview_url = $previewUrl;
            $approval->pdf_path    = $absPath;
        }
    }
}
