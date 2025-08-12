<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanKnowledge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanKnowledgeController extends Controller
{
    /**
     * Menampilkan daftar pengajuan sesuai hak akses.
     */
    public function index()
    {
        $user = Auth::user();

        // Cek jabatan user (tidak case-sensitive)
        $jabatan = strtoupper($user->jabatan_full ?? '');
        $isManager = str_contains($jabatan, 'MANAGER');
        $isDirector = str_contains($jabatan, 'DIRECTOR');
        
        $query = PengajuanKnowledge::with('creator')->latest();

        // Jika user adalah Manajer atau Direktur, mereka bisa melihat SEMUA pengajuan
        if ($isManager || $isDirector) {
            // Tidak perlu filter tambahan, ambil semua data
            $pengajuan = $query->get();
        } else {
            // Jika user biasa, filter hanya yang dibuat olehnya atau ditujukan kepadanya
            $pengajuan = $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('kepada', $user->name);
            })->get();
        }

        return view('pages.knowledge.pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        // Ambil semua user kecuali user yang sedang login untuk pilihan 'kepada'
        $users = User::where('id', '!=', Auth::id())->get();
        return view('pages.knowledge.pengajuan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:pengajuan_knowledge,kode',
            'kepada' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'pemateri' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lampiran' => 'nullable|mimes:pdf|max:2048',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,registration_id',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_knowledge', 'public');
        }

        // Ambil detail peserta
        $participants = [];
        if ($request->has('participants')) {
            $participantUsers = User::whereIn('registration_id', $request->participants)->get();
            foreach ($participantUsers as $user) {
                $participants[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'registration_id' => $user->registration_id,
                    'jabatan' => $user->jabatan->name ?? '-',
                    'department' => $user->department->name ?? '-'
                ];
            }
        }

        PengajuanKnowledge::create([
            'kode' => $request->kode,
            'created_by' => Auth::id(),
            'kepada' => $request->kepada,
            'dari' => Auth::user()->name,
            'perihal' => $request->perihal,
            'judul' => $request->judul,
            'pemateri' => $request->pemateri,
            'tanggal' => $request->tanggal,
            'peserta' => $participants,
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        return redirect()->route('knowledge.pengajuan.index')->with('success', 'Pengajuan berhasil disimpan.');
    }

    public function approve($id)
    {
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        // Pastikan hanya user yang dituju yang bisa menyetujui
        if ($pengajuan->kepada !== Auth::user()->name) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menyetujui pengajuan ini.');
        }

        $pengajuan->status = 'approved';
        $pengajuan->save();

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        // Pastikan hanya user yang dituju yang bisa menolak
        if ($pengajuan->kepada !== Auth::user()->name) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menolak pengajuan ini.');
        }

        $pengajuan->status = 'rejected';
        $pengajuan->rejection_reason = $request->rejection_reason;
        $pengajuan->save();

        return back()->with('error', 'Pengajuan ditolak.');
    }
}