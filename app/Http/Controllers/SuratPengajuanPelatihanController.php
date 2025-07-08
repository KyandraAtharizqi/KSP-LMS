<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SuratPengajuanPelatihan;
use App\Models\SuratPengajuanPelatihanSignature;
use App\Models\TrainingParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuratPengajuanPelatihanController extends Controller
{
    public function index()
    {
        $examples = SuratPengajuanPelatihan::with(['signatures.user', 'participants.user'])
            ->where('created_by', Auth::id())
            ->latest()
            ->get();

        return view('pages.training.suratpengajuan.index', compact('examples'));
    }

    public function create()
    {
        $users = User::all(); // Participant selection
        return view('pages.training.suratpengajuan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'surat_id' => 'required|string|unique:surat_pengajuan_pelatihan,surat_id',
            'kompetensi' => 'required|string',
            'judul' => 'required|string',
            'lokasi' => 'required|in:Perusahaan,Didalam Kota,Diluar Kota,Diluar Negeri',
            'instruktur' => 'required|in:Internal,Eksternal',
            'sifat' => 'required|in:Seminar,Kursus,Sertifikasi,Workshop',
            'kompetensi_wajib' => 'required|in:Wajib,Tidak Wajib',
            'materi_global' => 'required|string',
            'program_pelatihan_ksp' => 'required|in:Termasuk,Tidak Termasuk',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tempat' => 'required|string',
            'penyelenggara' => 'required|string',
            'biaya' => 'required|string',
            'per_paket_or_orang' => 'required|in:Paket,Orang',
            'keterangan' => 'nullable|string',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,registration_id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['durasi'] = Carbon::parse($validated['tanggal_mulai'])
            ->diffInDays(Carbon::parse($validated['tanggal_selesai'])) + 1;

        $surat = SuratPengajuanPelatihan::create($validated);

        // Add participants with snapshot data
        foreach ($validated['participants'] as $registrationId) {
            $user = User::where('registration_id', $registrationId)->first();
            if ($user) {
                TrainingParticipant::create([
                    'surat_id' => $surat->id,
                    'user_id' => $user->id,
                    'registration_id' => $user->registration_id,
                    'jabatan_id' => $user->jabatan_id,
                    'department_id' => $user->department_id,
                    'superior_id' => $user->superior_id,
                ]);
            }
        }

        // Signature approvals
        $this->insertApprovalSignatures($surat);

        return redirect()->route('training.suratpengajuan.index')
            ->with('success', 'Surat berhasil diajukan.');
    }

    protected function insertApprovalSignatures(SuratPengajuanPelatihan $surat)
    {
        $signatures = [];

        $first = $this->getFirstApprover($surat);
        $second = $this->getHumanCapitalManager();
        $third = $this->getDirectorHcFinance();

        if ($first) {
            $signatures[] = [
                'surat_id' => $surat->id,
                'user_id' => $first,
                'role' => 'mengusulkan',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($second) {
            $signatures[] = [
                'surat_id' => $surat->id,
                'user_id' => $second,
                'role' => 'mengetahui',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($third) {
            $signatures[] = [
                'surat_id' => $surat->id,
                'user_id' => $third,
                'role' => 'menyetujui',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        SuratPengajuanPelatihanSignature::insert($signatures);
    }

    protected function getFirstApprover(SuratPengajuanPelatihan $surat)
    {
        $firstParticipant = $surat->participants()->first();
        return $firstParticipant?->superior_id ?? Auth::id();
    }

    protected function getHumanCapitalManager()
    {
        return User::whereHas('department', fn($q) => $q->where('name', 'Human Capital'))
            ->whereHas('jabatan', fn($q) => $q->where('name', 'Manager'))
            ->first()?->id;
    }

    protected function getDirectorHcFinance()
    {
        return User::whereHas('department', fn($q) =>
            $q->whereIn('name', ['Human Capital', 'Finance']))
            ->whereHas('jabatan', fn($q) => $q->where('name', 'Director'))
            ->first()?->id;
    }

    public function preview($id)
    {
        $surat = SuratPengajuanPelatihan::with(['signatures.user', 'participants.user'])->findOrFail($id);
        return view('pages.training.suratpengajuan.preview', compact('surat'));
    }
}
