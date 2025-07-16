<?php

namespace App\Http\Controllers;

use App\Models\SuratTugasPelatihan;
use App\Models\SuratTugasPelatihanSignatureAndParaf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class SuratTugasPelatihanController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();

        $query = SuratTugasPelatihan::query()
            ->with(['creator', 'signaturesAndParafs.user', 'pelatihan']);

        if (!in_array($user->role, ['admin'])) {
            if (in_array($user->id, config('training.signature_users'))) {
                // no filter needed
            } elseif ($user->role === 'department_admin' && optional($user->department)->nama === 'Human Capital') {
                // no filter needed
            } else {
                abort(403);
            }
        }

        $suratTugasList = $query->latest()->get();
        $users = User::orderBy('name')->get();

        return view('pages.training.surattugas.index', compact('suratTugasList', 'users'));
    }

    public function preview($id): \Illuminate\Contracts\View\View
    {
        // Eager loading yang sudah diperbaiki
        $surat = SuratTugasPelatihan::with([
            'creator',
            'pelatihan.participants.user', // Untuk mengambil nama user
            'pelatihan.participants.jabatan', // Sekarang ini akan berfungsi
            'pelatihan.participants.department', // Sekarang ini akan berfungsi
            'signaturesAndParafs.user.jabatan',
        ])->findOrFail($id);

        return view('pages.training.surattugas.preview', compact('surat'));
    }

    public function assignView($id): \Illuminate\Contracts\View\View
    {
        $suratTugas = SuratTugasPelatihan::with('pelatihan')->findOrFail($id);
        $users = User::orderBy('name')->get();
        $user = Auth::user();

        $isAdmin = $user->role === 'admin';

        $isHumanCapitalManager = optional($user->department)->nama === 'Human Capital' &&
            strtolower(optional($user->jabatan)->nama) === 'manager';

        $isHcFinanceDirector = optional($user->directorate)->nama === 'Human Capital & Finance' &&
            strtolower(optional($user->jabatan)->nama) === 'director';

        if (!($isAdmin || $isHumanCapitalManager || $isHcFinanceDirector)) {
            abort(403);
        }

        return view('pages.training.surattugas.assign', compact('suratTugas', 'users'));
    }

    public function assignSave(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'surat_tugas_id' => 'required|exists:surat_tugas_pelatihans,id',
            'paraf_users' => 'required|array|min:1',
            'paraf_users.*' => 'exists:users,id',
            'signature_user' => 'required|exists:users,id',
        ]);

        $surat = SuratTugasPelatihan::findOrFail($request->surat_tugas_id);
        $user = Auth::user();

        $isAdmin = $user->role === 'admin';

        $isHumanCapitalManager = optional($user->department)->nama === 'Human Capital' &&
            strtolower(optional($user->jabatan)->nama) === 'manager';

        $isHcFinanceDirector = optional($user->directorate)->nama === 'Human Capital & Finance' &&
            strtolower(optional($user->jabatan)->nama) === 'director';

        if (!($isAdmin || $isHumanCapitalManager || $isHcFinanceDirector)) {
            abort(403);
        }

        $surat->signaturesAndParafs()->delete();

        $round = 1;
        $sequence = 1;

        foreach ($request->paraf_users as $userId) {
            SuratTugasPelatihanSignatureAndParaf::create([
                'surat_tugas_id' => $surat->id,
                'user_id' => $userId,
                'round' => $round,
                'sequence' => $sequence++,
                'type' => 'paraf',
                'status' => 'pending',
            ]);
        }

        SuratTugasPelatihanSignatureAndParaf::create([
            'surat_tugas_id' => $surat->id,
            'user_id' => $request->signature_user,
            'round' => $round,
            'sequence' => $sequence,
            'type' => 'signature',
            'status' => 'pending',
        ]);

        return redirect()->route('training.surattugas.index')->with('success', 'Penandatangan dan paraf berhasil ditetapkan.');
    }

    public function approveView($id, $approvalId): \Illuminate\Contracts\View\View
    {
        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat = SuratTugasPelatihan::with('pelatihan')->findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('pages.training.surattugas.approve', compact('surat', 'approval'));
    }

    public function approve(Request $request, $id, $approvalId): \Illuminate\Http\RedirectResponse
    {
        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat = SuratTugasPelatihan::findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            abort(403);
        }

        $approval->update([
            'status' => 'approved',
        ]);

        $pending = $surat->signaturesAndParafs()->where('status', 'pending')->count();

        if ($pending === 0) {
            $surat->update(['is_accepted' => true]);
        }

        return redirect()->route('training.surattugas.index')->with('success', 'Surat telah disetujui.');
    }

    public function rejectView($id, $approvalId): \Illuminate\Contracts\View\View
    {
        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat = SuratTugasPelatihan::findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('pages.training.surattugas.reject', compact('surat', 'approval'));
    }

    public function reject(Request $request, $id, $approvalId): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $approval = SuratTugasPelatihanSignatureAndParaf::findOrFail($approvalId);
        $surat = SuratTugasPelatihan::findOrFail($id);

        if ($approval->user_id !== Auth::id()) {
            abort(403);
        }

        $approval->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->route('training.surattugas.index')->with('warning', 'Surat ditolak.');
    }

    public function download($id)
    {
        // Eager loading yang sudah diperbaiki
        $surat = SuratTugasPelatihan::with([
            'pelatihan.participants.user',
            'pelatihan.participants.jabatan',
            'pelatihan.participants.department',
            'signaturesAndParafs.user.jabatan'
        ])->findOrFail($id);

        $pdf = PDF::loadView('pages.training.surattugas.pdf_view', ['surat' => $surat]);
        $namaFile = 'ST-' . ($surat->kode_tugas ?? '000') . '.pdf';
        return $pdf->download($namaFile);
    }
}
