<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    private $notificationTypes = [
        'knowledge_sharing' => 'App\Notifications\KnowledgeInvitation',
    ];

    // Ambil 5 notifikasi terbaru + jumlah yang belum dibaca
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        // kirim ke dashboard (atau halaman utama)
        return view('dashboard', compact('notifikasi', 'unreadCount'));
    }

    // Tandai notifikasi sudah dibaca
    public function markAsRead($id)
    {
        try {
            $notif = Notifikasi::find($id);

            // Jika notifikasi tidak ditemukan, kembalikan success=true dengan pesan khusus
            // sehingga UI tetap update meski notifikasi sudah tidak ada di DB
            if (!$notif) {
                \Log::info('Notification not found but marking as read anyway', ['id' => $id]);
                return response()->json([
                    'success' => true,
                    'unreadCount' => Notifikasi::where('user_id', Auth::id())->where('dibaca', false)->count(),
                    'message' => 'Notifikasi sudah ditandai dibaca (auto)'
                ]);
            }

            // Jika notifikasi bukan milik user yang login
            if ($notif->user_id != Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan'], 404);
            }

            $notif->dibaca = true;
            $notif->save();

            $unreadCount = Notifikasi::where('user_id', Auth::id())
                ->where('dibaca', false)
                ->count();

            return response()->json([
                'success' => true,
                'unreadCount' => $unreadCount,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    // Tandai semua notifikasi sudah dibaca
    public function markAllAsRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'unreadCount' => 0
        ]);
    }

    // Kirim notifikasi
    public function sendNotification($userId, $type, $data)
    {
        try {
            $user = \App\Models\User::find($userId);
            
            if (!$user) {
                throw new \Exception('User not found');
            }

            if (!isset($this->notificationTypes[$type])) {
                throw new \Exception('Invalid notification type');
            }

            $notificationClass = $this->notificationTypes[$type];
            $notification = new $notificationClass($data);

            $user->notify($notification);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Hapus notifikasi
    public function delete($id)
    {
        \Log::info('Delete notification request - direct route', [
            'notif_id' => $id,
            'user_id' => Auth::id(),
            'url' => request()->fullUrl(),
            'method' => request()->method()
        ]);

        try {
            $notif = Notifikasi::find($id);

            if ($notif) {
                \Log::info('Notif found', [
                    'notif_user_id' => $notif->user_id,
                    'current_user_id' => Auth::id(),
                    'notif_data' => $notif->toArray()
                ]);
            } else {
                \Log::warning('Notif not found', [
                    'notif_id' => $id
                ]);
                // Auto-clear dari navbar jika notifikasi sudah tidak ada
                return response()->json([
                    'success' => true,
                    'message' => 'Notification already deleted',
                    'unreadCount' => Notifikasi::where('user_id', Auth::id())->where('dibaca', false)->count()
                ]);
            }

            if ($notif->user_id == Auth::id()) {
                $notif->delete();

                $unreadCount = Notifikasi::where('user_id', Auth::id())
                    ->where('dibaca', false)
                    ->count();

                \Log::info('Notif deleted', [
                    'notif_id' => $id
                ]);

                return response()->json([
                    'success' => true,
                    'unreadCount' => $unreadCount
                ]);
            }

            \Log::warning('Notif not deleted - user mismatch', [
                'notif_id' => $id,
                'notif_user_id' => $notif->user_id,
                'current_user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'User not authorized to delete this notification'
            ], 403);
            
        } catch (\Exception $e) {
            \Log::error('Exception in delete notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
