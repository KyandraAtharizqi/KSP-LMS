<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanKnowledge;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuratUndanganController extends Controller
{
    public function index()
    {
        $me = Auth::user();

        // Ambil semua pengajuan yang sudah disetujui
        $undangan = PengajuanKnowledge::where('status', 'approved')->get();

        if ($me->role === 'admin') {
            // Admin bisa lihat semua undangan (draft dan sent)
            $filtered = $undangan;
        } else {
            // Filter berdasarkan peran user
            $filtered = $undangan->filter(function ($u) use ($me) {
                $participantIds = collect($u->peserta ?? [])->pluck('id')->all();
                
                $isCreator = $me->id === $u->created_by;
                $isApprover = $me->name === $u->kepada;
                $isParticipant = in_array($me->id, $participantIds);
                
                // Pengaju dan Approver bisa melihat semua undangan (draft dan sent)
                if ($isCreator || $isApprover) {
                    return true;
                }
                
                // Peserta hanya bisa melihat undangan yang sudah dikirim
                if ($isParticipant) {
                    return $u->status_undangan === 'sent';
                }
                
                return false;
            });
        }

        // Kumpulkan semua peserta dari hasil filter
        $userIds = $filtered->flatMap(function ($u) {
            return collect($u->peserta ?? [])->pluck('id');
        })->unique();

        // Ambil data peserta
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        return view('pages.knowledge.undangan.index', [
            'undangan' => $filtered,
            'users' => $users,
        ]);
    }

    public function edit($id)
    {
        $undangan = PengajuanKnowledge::findOrFail($id);
        
        // Cek apakah user berhak mengedit
        $me = Auth::user();
        if ($me->role !== 'admin' && $me->id !== $undangan->created_by && $me->name !== $undangan->kepada) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit undangan ini.');
        }

        return view('pages.knowledge.undangan.edit', [
            'undangan' => $undangan
        ]);
    }

    public function update(Request $request, $id)
    {
        $undangan = PengajuanKnowledge::findOrFail($id);
        
        // Cek apakah user berhak mengedit
        $me = Auth::user();
        if ($me->role !== 'admin' && $me->id !== $undangan->created_by && $me->name !== $undangan->kepada) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit undangan ini.');
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string',
        ]);

        $undangan->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('knowledge.undangan.index')
            ->with('success', 'Tanggal undangan berhasil diperbarui.');
    }

    public function show($id)
    {
        $undangan = PengajuanKnowledge::findOrFail($id);
        
        // Cek apakah user berhak melihat
        $me = Auth::user();
        $participantIds = collect($undangan->peserta ?? [])->pluck('id')->all();
        
        if ($me->role !== 'admin' && 
            $me->id !== $undangan->created_by && 
            $me->name !== $undangan->kepada && 
            !in_array($me->id, $participantIds)) {
            abort(403, 'Anda tidak memiliki izin untuk melihat undangan ini.');
        }

        return view('pages.knowledge.undangan.undangan', [
            'undangan' => $undangan
        ]);
    }

    public function send($id)
    {
        \Log::info('Send undangan request received', [
            'id' => $id,
            'user' => Auth::user()->name,
            'method' => request()->method()
        ]);

        $undangan = PengajuanKnowledge::findOrFail($id);
        
        \Log::info('Undangan found', [
            'undangan_id' => $undangan->id,
            'status' => $undangan->status,
            'status_undangan' => $undangan->status_undangan,
            'peserta_count' => count($undangan->peserta ?? [])
        ]);
        
        // Cek apakah user berhak mengirim
        $me = Auth::user();
        if ($me->role !== 'admin' && $me->id !== $undangan->created_by && $me->name !== $undangan->kepada) {
            \Log::warning('User not authorized to send invitation', [
                'user_id' => $me->id,
                'user_name' => $me->name,
                'user_role' => $me->role,
                'created_by' => $undangan->created_by,
                'kepada' => $undangan->kepada
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengirim undangan ini.'
            ], 403);
        }

        // Cek apakah undangan masih dalam status draft
        if ($undangan->status_undangan !== 'draft') {
            \Log::warning('Undangan status not draft', [
                'status_undangan' => $undangan->status_undangan
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Undangan sudah pernah dikirim sebelumnya.'
            ]);
        }

        try {
            \Log::info('Starting to send notifications to participants', [
                'peserta_count' => count($undangan->peserta ?? [])
            ]);

            // Kirim notifikasi ke semua peserta
            if (!empty($undangan->peserta)) {
                foreach ($undangan->peserta as $peserta) {
                    if (isset($peserta['id'])) {
                        \Log::info('Creating notification for participant', [
                            'participant_id' => $peserta['id'],
                            'participant_name' => $peserta['name'] ?? 'Unknown'
                        ]);

                        $tanggalMulai = Carbon::parse($undangan->tanggal_mulai)->format('d M Y');
                        $jamMulai = $undangan->jam_mulai ?? '';
                        $jamSelesai = $undangan->jam_selesai ?? '';
                        
                        $waktu = $tanggalMulai;
                        if ($jamMulai && $jamSelesai) {
                            $waktu .= " pukul {$jamMulai} - {$jamSelesai} WIB";
                        } elseif ($jamMulai) {
                            $waktu .= " pukul {$jamMulai} WIB";
                        }

                        Notifikasi::create([
                            'user_id' => $peserta['id'],
                            'judul' => 'Undangan Knowledge Sharing',
                            'pesan' => "Anda diundang untuk mengikuti Kegiatan Knowledge Sharing '{$undangan->judul}' oleh {$undangan->pemateri} yang akan dilaksanakan pada {$waktu}",
                            'link' => route('knowledge.undangan.show', $undangan->id)
                        ]);
                    }
                }
            }

            \Log::info('Updating undangan status to sent');
            
            // Update status undangan menjadi sent
            $undangan->update([
                'status_undangan' => 'sent'
            ]);

            \Log::info('Undangan successfully sent', [
                'undangan_id' => $undangan->id,
                'new_status' => $undangan->fresh()->status_undangan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Undangan berhasil dikirim ke semua peserta.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending invitation notifications: ' . $e->getMessage(), [
                'undangan_id' => $undangan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim undangan.'
            ], 500);
        }
    }
}
