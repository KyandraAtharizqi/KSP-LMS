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
        // ambil pengajuan yang disetujui
        $undangan = PengajuanKnowledge::where('status', 'approved')->get();

        // ambil semua user peserta sekaligus
        $userIds = [];
        foreach($undangan as $u){
            foreach($u->peserta ?? [] as $p){
                if(is_array($p) && isset($p['id'])){
                    $userIds[] = $p['id'];
                }
            }
        }
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // filter hanya yang boleh melihat: admin, dari, kepada, peserta
        $filtered = $undangan->filter(function($u) use ($users){
            $me = auth()->user();
            $participantIds = collect($u->peserta ?? [])->map(fn($p) => $p['id'] ?? null);
            return $me->role == 'admin' 
                || $me->name == $u->dari 
                || $me->name == $u->kepada 
                || $participantIds->contains($me->id);
        });

        return view('pages.knowledge.undangan.index', [
            'undangan' => $filtered,
            'users' => $users
        ]);
    }
}
