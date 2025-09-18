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
        $me = Auth::user();

        // Ambil semua pengajuan dengan relasi creator
        $pengajuan = PengajuanKnowledge::with('creator')->latest()->get();

        if ($me->role !== 'admin') {
            $pengajuan = $pengajuan->filter(function ($p) use ($me) {
                return $p->created_by == $me->id
                    || $p->kepada == $me->name; // ⚡ Peserta tidak lagi diizinkan
            });
        }

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
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
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
            'jam_mulai' => $request->waktu_mulai,
            'jam_selesai' => $request->waktu_selesai,
            'peserta' => $participants,
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke user "kepada"
        $kepadaUser = User::where('name', $request->kepada)->first();
        if ($kepadaUser) {
            Notifikasi::create([
                'user_id' => $kepadaUser->id,
                'judul' => 'Pengajuan Knowledge Sharing Baru',
                'pesan' => "Anda menerima pengajuan knowledge sharing baru dengan judul '{$request->judul}' dari " . auth()->user()->name,
                'link' => route('knowledge.pengajuan.preview', $pengajuan->id),
                'dibaca' => false
            ]);
        }

        return redirect()->route('knowledge.pengajuan.index')
                        ->with('success', 'Pengajuan berhasil disimpan.');
    }

    public function approve($id)
    {
        \Log::info('Approve request received', [
            'id' => $id,
            'method' => request()->method(),
            'input' => request()->all()
        ]);

        $pengajuan = PengajuanKnowledge::findOrFail($id);

        if ($pengajuan->kepada !== Auth::user()->name) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menyetujui pengajuan ini.');
        }

        $pengajuan->status = 'approved';
        $pengajuan->status_undangan = 'draft'; // Set sebagai draft, belum dikirim
        $pengajuan->save();

        \Log::info('Pengajuan approved - status undangan set to draft', [
            'pengajuan_id' => $pengajuan->id,
            'status' => $pengajuan->status,
            'status_undangan' => $pengajuan->status_undangan,
            'message' => 'NO AUTOMATIC NOTIFICATIONS SENT TO PARTICIPANTS'
        ]);

        // Tambahkan notifikasi ke pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->created_by,
            'judul' => 'Pengajuan Knowledge Sharing Disetujui',
            'pesan' => "Pengajuan kamu '{$pengajuan->perihal}' telah disetujui oleh " . Auth::user()->name . ". Undangan akan dikirim setelah tanggal dikonfirmasi.",
            'link' => route('knowledge.pengajuan.preview', $pengajuan->id)
        ]);

        // CATATAN: Notifikasi ke peserta tidak dikirim otomatis lagi
        // Notifikasi akan dikirim melalui tombol "Kirim ke Peserta" di halaman undangan

        return back()->with('success', 'Pengajuan disetujui. Undangan dalam status draft dan perlu dikirim manual.');
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
            'judul' => 'Pengajuan Knowledge Sharing Ditolak',
            'pesan' => "Pengajuan kamu '{$pengajuan->perihal}' ditolak oleh " . Auth::user()->name . ". Alasan: " . $request->rejection_reason,
            'link' => route('knowledge.pengajuan.preview', $pengajuan->id)
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
        $pengajuan->jam_mulai = $request->waktu_mulai;
        $pengajuan->jam_selesai = $request->waktu_selesai;

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

        // Kirim notifikasi hanya ke peserta baru JIKA pengajuan masih pending
        // Jika sudah approved, notifikasi akan dikirim melalui sistem undangan
        if ($pengajuan->status === 'pending') {
            $existingIds = collect($pengajuan->peserta ?? [])->pluck('id')->toArray();
            foreach ($participantUsers as $user) {
                if (!in_array($user->id, $existingIds)) {
                    $user->notify(new KnowledgeInvitation($pengajuan));
                }
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
