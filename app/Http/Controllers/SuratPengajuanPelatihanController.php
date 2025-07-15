<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SuratPengajuanPelatihan;
use App\Models\TrainingParticipant;
use App\Models\SuratPengajuanPelatihanSignatureAndParaf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratPengajuanPelatihanController extends Controller
{
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

    public function create()
    {
        $users = User::all();
        return view('pages.training.suratpengajuan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelatihan' => 'required|string|unique:surat_pengajuan_pelatihans,kode_pelatihan',
            'kompetensi' => 'required|string',
            'judul' => 'required|string',
            'lokasi' => 'required|string',
            'instruktur' => 'required|string',
            'sifat' => 'required|string',
            'kompetensi_wajib' => 'required|string',
            'materi_global' => 'required|string',
            'program_pelatihan_ksp' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'durasi' => 'required|integer|min:1',
            'tempat' => 'required|string',
            'penyelenggara' => 'required|string',
            'biaya' => 'required|string',
            'per_paket_or_orang' => 'required|string',
            'keterangan' => 'nullable|string',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,registration_id',
            'parafs' => 'nullable|array|max:3',
            'parafs.*' => 'exists:users,registration_id',
            'signatures' => 'required|array|min:1',
            'signatures.*' => 'exists:users,registration_id',
            'signature2' => 'nullable|array|max:1',
            'signature2.*' => 'exists:users,registration_id',
            'signature3' => 'nullable|array|max:1',
            'signature3.*' => 'exists:users,registration_id',
        ]);

        DB::beginTransaction();

        try {
            $surat = SuratPengajuanPelatihan::create([
                'created_by' => auth()->id(),
                'kode_pelatihan' => $validated['kode_pelatihan'],
                'kompetensi' => $validated['kompetensi'],
                'judul' => $validated['judul'],
                'lokasi' => $validated['lokasi'],
                'instruktur' => $validated['instruktur'],
                'sifat' => $validated['sifat'],
                'kompetensi_wajib' => $validated['kompetensi_wajib'],
                'materi_global' => $validated['materi_global'],
                'program_pelatihan_ksp' => $validated['program_pelatihan_ksp'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'durasi' => $validated['durasi'],
                'tempat' => $validated['tempat'],
                'penyelenggara' => $validated['penyelenggara'],
                'biaya' => $validated['biaya'],
                'per_paket_or_orang' => $validated['per_paket_or_orang'],
                'keterangan' => $validated['keterangan'],
            ]);

            foreach ($validated['participants'] as $registrationId) {
                $user = User::where('registration_id', $registrationId)->first();
                if ($user) {
                    TrainingParticipant::create([
                        'pelatihan_id' => $surat->id,
                        'user_id' => $user->id,
                        'kode_pelatihan' => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'jabatan_id' => $user->jabatan_id,
                        'department_id' => $user->department_id,
                        'superior_id' => $user->superior_id,
                    ]);
                }
            }

            $sequence = 1;
            $round = 1;

            foreach ($validated['parafs'] ?? [] as $registrationId) {
                $user = User::where('registration_id', $registrationId)->first();
                if ($user) {
                    SuratPengajuanPelatihanSignatureAndParaf::create([
                        'pelatihan_id' => $surat->id,
                        'user_id' => $user->id,
                        'kode_pelatihan' => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'round' => $round,
                        'sequence' => $sequence++,
                        'type' => 'paraf',
                        'status' => 'pending',
                    ]);
                }
            }

            foreach (['signatures', 'signature2', 'signature3'] as $key) {
                foreach ($validated[$key] ?? [] as $registrationId) {
                    $user = User::where('registration_id', $registrationId)->first();
                    if ($user) {
                        SuratPengajuanPelatihanSignatureAndParaf::create([
                            'pelatihan_id' => $surat->id,
                            'user_id' => $user->id,
                            'kode_pelatihan' => $surat->kode_pelatihan,
                            'registration_id' => $user->registration_id,
                            'round' => $round,
                            'sequence' => $sequence++,
                            'type' => 'signature',
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('training.suratpengajuan.index')->with('success', 'Surat pengajuan pelatihan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan surat: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
    $surat = SuratPengajuanPelatihan::with([
        'creator',
        'participants.user',
        'approvals.user'
    ])->findOrFail($id);

    // Only allow creator to edit if last round is rejected
    if (auth()->id() !== $surat->created_by) {
        abort(403);
    }

    $latestRound = $surat->approvals->max('round');
    $latestRejection = $surat->approvals
        ->where('round', $latestRound)
        ->firstWhere('status', 'rejected');

    $users = User::all();

    return view('pages.training.suratpengajuan.edit', compact('surat', 'users', 'latestRejection'));
    }


    public function preview($id)
    {
        $surat = SuratPengajuanPelatihan::with([
            'creator',
            'participants.user',
            'approvals.user',
        ])->findOrFail($id);

        foreach ($surat->approvals as $approval) {
            $registrationId = $approval->registration_id;

            $sigRecord = DB::table('signature_and_parafs')
                ->where('registration_id', $registrationId)
                ->first();

            if ($sigRecord) {
                if ($approval->type === 'paraf') {
                    $approval->image_path = str_replace('storage/', '', $sigRecord->paraf_path);
                } elseif ($approval->type === 'signature') {
                    $approval->image_path = str_replace('storage/', '', $sigRecord->signature_path);
                }
            } else {
                $approval->image_path = null;
            }
        }

        return view('pages.training.suratpengajuan.preview', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kompetensi' => 'required|string',
            'judul' => 'required|string',
            'lokasi' => 'required|string',
            'instruktur' => 'required|string',
            'sifat' => 'required|string',
            'kompetensi_wajib' => 'required|string',
            'materi_global' => 'required|string',
            'program_pelatihan_ksp' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'durasi' => 'required|integer|min:1',
            'tempat' => 'required|string',
            'penyelenggara' => 'required|string',
            'biaya' => 'required|string',
            'per_paket_or_orang' => 'required|string',
            'keterangan' => 'nullable|string',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,registration_id',
            'parafs' => 'nullable|array|max:3',
            'parafs.*' => 'exists:users,registration_id',
            'signatures' => 'required|array|min:1',
            'signatures.*' => 'exists:users,registration_id',
            'signature2' => 'nullable|array|max:1',
            'signature2.*' => 'exists:users,registration_id',
            'signature3' => 'nullable|array|max:1',
            'signature3.*' => 'exists:users,registration_id',
        ]);

        $surat = SuratPengajuanPelatihan::findOrFail($id);

        if (auth()->id() !== $surat->created_by) {
            abort(403);
        }

        $latestRound = $surat->approvals()->max('round') ?? 1;

        // Check if latest round contains any rejection
        $rejected = $surat->approvals()
            ->where('round', $latestRound)
            ->where('status', 'rejected')
            ->exists();

        if (!$rejected) {
            return back()->with('error', 'Surat tidak dapat diubah karena belum ada penolakan pada round terakhir.');
        }

        DB::beginTransaction();

        try {
            // Update surat fields
            $surat->update($validated);

            // Replace participants
            $surat->participants()->delete();
            foreach ($validated['participants'] as $registrationId) {
                $user = User::where('registration_id', $registrationId)->first();
                if ($user) {
                    TrainingParticipant::create([
                        'pelatihan_id' => $surat->id,
                        'user_id' => $user->id,
                        'kode_pelatihan' => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'jabatan_id' => $user->jabatan_id,
                        'department_id' => $user->department_id,
                        'superior_id' => $user->superior_id,
                    ]);
                }
            }

            // Create a new round
            $nextRound = $latestRound + 1;
            $sequence = 1;

            foreach ($validated['parafs'] ?? [] as $registrationId) {
                $user = User::where('registration_id', $registrationId)->first();
                if ($user) {
                    SuratPengajuanPelatihanSignatureAndParaf::create([
                        'pelatihan_id' => $surat->id,
                        'user_id' => $user->id,
                        'kode_pelatihan' => $surat->kode_pelatihan,
                        'registration_id' => $user->registration_id,
                        'round' => $nextRound,
                        'sequence' => $sequence++,
                        'type' => 'paraf',
                        'status' => 'pending',
                    ]);
                }
            }

            foreach (['signatures', 'signature2', 'signature3'] as $key) {
                foreach ($validated[$key] ?? [] as $registrationId) {
                    $user = User::where('registration_id', $registrationId)->first();
                    if ($user) {
                        SuratPengajuanPelatihanSignatureAndParaf::create([
                            'pelatihan_id' => $surat->id,
                            'user_id' => $user->id,
                            'kode_pelatihan' => $surat->kode_pelatihan,
                            'registration_id' => $user->registration_id,
                            'round' => $nextRound,
                            'sequence' => $sequence++,
                            'type' => 'signature',
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            // ❗Optional cleanup: mark all still pending approvals in previous round as rejected
            $surat->approvals()
                ->where('round', $latestRound)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Auto rejected by resubmission.',
                    'signed_at' => now(),
                ]);

            DB::commit();
            return redirect()->route('training.suratpengajuan.index')->with('success', 'Surat berhasil diperbarui dan diajukan ulang.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan surat: ' . $e->getMessage());
        }
    }




    public function approve($suratId, $approvalId)
    {
        $approval = SuratPengajuanPelatihanSignatureAndParaf::where('id', $approvalId)
            ->where('pelatihan_id', $suratId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $approval->update([
            'status' => 'approved',
            'signed_at' => now(),
        ]);

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

        DB::transaction(function () use ($approval, $request) {
            $approval->update([
                'status' => 'rejected',
                'signed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // ❗ Reject all later approvals in same round
            SuratPengajuanPelatihanSignatureAndParaf::where('pelatihan_id', $approval->pelatihan_id)
                ->where('round', $approval->round)
                ->where('sequence', '>', $approval->sequence)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'signed_at' => now(),
                    'rejection_reason' => 'Auto rejected due to earlier rejection',
                ]);
        });

        return back()->with('danger', 'Surat telah ditolak dan dikembalikan ke pembuat.');
    }
}
