<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanKnowledge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SuratUndanganController extends Controller
{
    public function index()
    {
        $me = Auth::user();

        // Ambil semua pengajuan yang sudah disetujui
        $undangan = PengajuanKnowledge::where('status', 'approved')->get();

        if ($me->role === 'admin') {
            // Admin bisa lihat semua
            $filtered = $undangan;
        } else {
            // Filter hanya undangan yang relevan
            $filtered = $undangan->filter(function ($u) use ($me) {
                $participantIds = collect($u->peserta ?? [])->pluck('id');
                return $me->name === $u->dari
                    || $me->name === $u->kepada
                    || $participantIds->contains($me->id);
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
}
