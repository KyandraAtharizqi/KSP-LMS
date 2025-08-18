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

    // Method untuk ambil notifikasi terbaru dan jumlah yang belum dibaca
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login'); // Pastikan user login
        }

        // Ambil 5 notifikasi terbaru
        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung jumlah yang belum dibaca
        $unreadCount = Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        // Kembalikan ke view navbar atau bisa JSON untuk AJAX
        return view('components.navbar', compact('notifikasi', 'unreadCount'));
    }

    // Method untuk menandai notifikasi sudah dibaca
    public function markAsRead($id)
    {
        $notif = Notifikasi::find($id);

        if ($notif && $notif->user_id == Auth::id()) {
            $notif->dibaca = true;
            $notif->save();
        }

        return response()->json(['success' => true]);
    }

    // Method untuk mengirim notifikasi ke user
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
            
            // Kirim notifikasi menggunakan queue
            $user->notify($notification);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
