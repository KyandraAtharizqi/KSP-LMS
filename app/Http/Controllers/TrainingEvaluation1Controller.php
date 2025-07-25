<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuanPelatihan;
use App\Models\EvaluasiLevel1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingEvaluation1Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        $pelatihansQuery = SuratPengajuanPelatihan::with('suratTugas', 'daftarHadir', 'evaluasiLevel1')
            ->whereHas('suratTugas', function ($query) {
                $query->where('is_accepted', true);
            });

        // Jika bukan admin, filter hanya pelatihan yang dia hadiri
        if (! $user->hasRole('admin')) {
            $pelatihansQuery->whereHas('daftarHadir', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'hadir');
            });
        }

        $pelatihans = $pelatihansQuery->latest()->get();

        return view('pages.training.evaluasi1.index', [
            'pelatihans' => $pelatihans,
        ]);
    }

    public function show($id)
    {
        $pelatihan = SuratPengajuanPelatihan::with('daftarHadirStatus', 'daftarHadir')->findOrFail($id);

        // Ambil presenter dari daftar hadir status yang tidak null
        $presenters = $pelatihan->daftarHadirStatus
            ->whereNotNull('presenter')
            ->pluck('presenter')
            ->unique()
            ->values();

        // Hanya user yang hadir yang bisa isi evaluasi
        $userId = auth()->id();
        $sudahHadir = $pelatihan->daftarHadir
            ->where('user_id', $userId)
            ->where('status', 'hadir')
            ->isNotEmpty();

        if (! $sudahHadir) {
            return redirect()->route('training.evaluation1.index')
                ->with('error', 'Anda belum hadir di pelatihan ini, tidak dapat mengisi evaluasi.');
        }

        return view('pages.training.evaluasi1.show', compact('pelatihan', 'presenters'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'materi' => 'required|string',
            'narasumber' => 'required|string',
        ]);

        EvaluasiLevel1::updateOrCreate(
            [
                'pelatihan_id' => $id,
                'user_id' => auth()->id()
            ],
            [
                'materi' => $request->materi,
                'narasumber' => $request->narasumber,
            ]
        );

        return redirect()->route('training.evaluation1.index')->with('success', 'Evaluasi berhasil dikirim.');
    }

}
