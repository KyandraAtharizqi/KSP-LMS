<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuanPelatihan;
use App\Models\DaftarHadirPelatihanStatus;
use Illuminate\Support\Facades\Auth;

class EvaluasiLevel1Controller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua pelatihan di mana user adalah peserta
        $pelatihans = SuratPengajuanPelatihan::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        // Filter: semua daftar hadir sudah is_submitted
        ->whereDoesntHave('daftarHadirPelatihanStatus', function ($q) {
            $q->where('is_submitted', false);
        })
        ->with(['evaluasiLevel1', 'daftarHadirPelatihanStatus'])
        ->get();

        return view('pages.training.evaluasi_level_1.index', compact('pelatihans'));
    }
}
