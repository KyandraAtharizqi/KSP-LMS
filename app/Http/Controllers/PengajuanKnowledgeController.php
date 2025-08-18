<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanKnowledge;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use App\Notifications\KnowledgeInvitation;
use Illuminate\Support\Facades\Storage;


class PengajuanKnowledgeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $jabatan = strtoupper($user->jabatan_full ?? '');
        $isManager = str_contains($jabatan, 'MANAGER');
        $isDirector = str_contains($jabatan, 'DIRECTOR');

        $query = PengajuanKnowledge::with('creator')->latest();

        if (!($isManager || $isDirector)) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('kepada', $user->name);
            });
        }

        $pengajuan = $query->get();

        return view('pages.knowledge.pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('pages.knowledge.pengajuan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:knowledge_pengajuan,kode',
            'kepada' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'pemateri' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'lampiran' => 'nullable|mimes:pdf|max:2048',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,registration_id',
        ]);

        // Simpan lampiran jika ada
        $lampiranPath = $request->hasFile('lampiran') 
            ? $request->file('lampiran')->store('lampiran_knowledge', 'public') 
            : null;

        // Ambil peserta berdasarkan registration_id
        $participants = [];
        if ($request->filled('participants')) {
            $users = User::whereIn('registration_id', $request->participants)->get();
            foreach ($users as $u) {
                $participants[] = [
                    'id' => $u->id,
                    'name' => $u->name,
                    'registration_id' => $u->registration_id,
                    'jabatan' => $u->jabatan_full ?? '-',
                    'department' => optional($u->department)->name ?? '-',
                ];
            }
        }

        // Simpan pengajuan
        $pengajuan = PengajuanKnowledge::create([
            'kode' => $request->kode,
            'created_by' => auth()->id(),
            'kepada' => $request->kepada,
            'dari' => auth()->user()->name,
            'perihal' => $request->perihal,
            'judul' => $request->judul,
            'pemateri' => $request->pemateri,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'peserta' => $participants,
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke peserta
        if (!empty($participants)) {
            foreach ($users as $user) {
                $user->notify(new KnowledgeInvitation($pengajuan));
            }
        }

        // Kirim notifikasi ke user "kepada"
        $kepadaUser = User::where('name', $request->kepada)->first();
        if ($kepadaUser) {
            $kepadaUser->notify(new KnowledgeInvitation($pengajuan));
        }

        dd('sampai sini');
        return redirect()->route('knowledge.pengajuan.index')
                        ->with('success', 'Pengajuan berhasil disimpan.');
    }

    public function approve($id)
    {
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        if ($pengajuan->kepada !== Auth::user()->name) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menyetujui pengajuan ini.');
        }

        $pengajuan->status = 'approved';
        $pengajuan->save();

        // Tambahkan notifikasi ke pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->created_by,
            'message' => "Pengajuan kamu '{$pengajuan->judul}' telah disetujui oleh " . Auth::user()->name,
        ]);

        return back()->with('success', 'Pengajuan disetujui dan notifikasi dikirim.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        $pengajuan = PengajuanKnowledge::findOrFail($id);

        if ($pengajuan->kepada !== Auth::user()->name) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menolak pengajuan ini.');
        }

        $pengajuan->status = 'rejected';
        $pengajuan->rejection_reason = $request->rejection_reason;
        $pengajuan->save();

        // Opsional: kirim notifikasi ke pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->created_by,
            'message' => "Pengajuan kamu '{$pengajuan->judul}' ditolak oleh " . Auth::user()->name,
        ]);

        return back()->with('error', 'Pengajuan ditolak dan notifikasi dikirim.');
    }

    public function preview($id)
    {
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        $pengaju = User::find($pengajuan->created_by);
        $pengajuan->dari = $pengaju ? $pengaju->name . ' (' . $pengaju->jabatan_full . ')' : $pengajuan->dari;

        $pesertaList = [];
        foreach ($pengajuan->peserta ?? [] as $p) {
            if (is_array($p) && isset($p['id'])) {
                $user = User::find($p['id']);
                $pesertaList[] = $user ? $user->name . ' (' . $user->jabatan_full . ')' : ($p['name'] ?? json_encode($p));
            } else {
                $user = User::find($p);
                $pesertaList[] = $user ? $user->name . ' (' . $user->jabatan_full . ')' : $p;
            }
        }
        $pengajuan->pesertaList = $pesertaList;

        return view('pages.knowledge.pengajuan.preview', compact('pengajuan'));
    }

    public function edit($id)
    {
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        if ($pengajuan->created_by != auth()->id() || $pengajuan->status != 'pending') {
            abort(403, 'Anda tidak diizinkan mengedit pengajuan ini.');
        }

        $users = User::with('department')
            ->orderBy('name')
            ->get(['id','name','registration_id','jabatan_full','department_id']);

        return view('pages.knowledge.pengajuan.edit', compact('pengajuan','users'));
    }

    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        if ($pengajuan->created_by != auth()->id() || $pengajuan->status != 'pending') {
            abort(403, 'Anda tidak diizinkan mengedit pengajuan ini.');
        }

        $request->validate([
            'perihal' => 'required|string|max:255',
            'pemateri' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'lampiran' => 'nullable|file|mimes:pdf|max:2048',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,registration_id',
        ]);

        $pengajuan->perihal = $request->perihal;
        $pengajuan->pemateri = $request->pemateri;
        $pengajuan->tanggal_mulai = $request->tanggal_mulai;
        $pengajuan->tanggal_selesai = $request->tanggal_selesai;

        // Update lampiran jika ada
        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lampiran_knowledge', 'public');
            $pengajuan->lampiran = $path;
        }

        // Update peserta
        $participants = [];
        $participantUsers = collect();

        if ($request->filled('participants')) {
            $participantUsers = User::whereIn('registration_id', $request->participants)->get();
            foreach ($participantUsers as $u) {
                $participants[] = [
                    'id' => $u->id,
                    'name' => $u->name,
                    'registration_id' => $u->registration_id,
                    'jabatan' => $u->jabatan_full ?? '-',
                    'department' => optional($u->department)->name ?? '-',
                ];
            }
        }

        // Kirim notifikasi hanya ke peserta baru
        $existingIds = collect($pengajuan->peserta ?? [])->pluck('id')->toArray();
        foreach ($participantUsers as $user) {
            if (!in_array($user->id, $existingIds)) {
                $user->notify(new KnowledgeInvitation($pengajuan));
            }
        }

        $pengajuan->peserta = $participants;
        $pengajuan->save();

        return redirect()->route('knowledge.pengajuan.index')
                        ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanKnowledge::findOrFail($id);

        if ($pengajuan->created_by != auth()->id() || $pengajuan->status != 'pending') {
            abort(403, 'Anda tidak diizinkan menghapus pengajuan ini.');
        }

        if ($pengajuan->lampiran && Storage::disk('public')->exists($pengajuan->lampiran)) {
            Storage::disk('public')->delete($pengajuan->lampiran);
        }

        $pengajuan->delete();

        return redirect()->route('knowledge.pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
