<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class Navbar extends Component
{
    public $notifikasi;
    public $unreadCount;
    public $search;

    public function __construct()
    {
        $user = Auth::user();
        
        if ($user) {
            // Bersihkan notifikasi invalid terlebih dahulu
            $this->cleanInvalidNotifications($user->id);

            $this->notifikasi = Notifikasi::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $this->unreadCount = Notifikasi::where('user_id', $user->id)
                ->where('dibaca', false)
                ->count();
        } else {
            $this->notifikasi = collect();
            $this->unreadCount = 0;
        }

        $this->search = old('search', request()->get('search'));
    }

    public function render()
    {
        return view('components.navbar');
    }
    
    /**
     * Bersihkan notifikasi yang sudah tidak valid
     * (misal: pengajuan sudah dihapus tapi notifikasi masih ada)
     */
    private function cleanInvalidNotifications($userId)
    {
        try {
            // Hapus notifikasi knowledge sharing yang pengajuannya sudah tidak ada
            Notifikasi::where('user_id', $userId)
                ->where(function($query) {
                    $query->where('judul', 'like', '%Undangan Knowledge Sharing%')
                          ->orWhere('judul', 'like', '%Pengajuan Knowledge Sharing%');
                })
                ->whereNotNull('link')
                ->get()
                ->each(function($notif) {
                    // Cek apakah link mengandung route knowledge.pengajuan.preview
                    if (strpos($notif->link, 'knowledge/pengajuan/') !== false) {
                        // Ekstrak ID dari URL
                        $parts = explode('/', $notif->link);
                        $id = end($parts);
                        
                        // Cek apakah pengajuan masih ada
                        if (!is_numeric($id) || !\App\Models\PengajuanKnowledge::find($id)) {
                            // Hapus notifikasi jika pengajuan sudah tidak ada
                            $notif->delete();
                        }
                    }
                });
        } catch (\Exception $e) {
            // Log error tapi jangan biarkan crash
            \Log::error('Error cleaning invalid notifications: ' . $e->getMessage());
        }
    }
}
