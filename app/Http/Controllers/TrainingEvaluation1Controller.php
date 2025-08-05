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

    public function form($id)
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
            return redirect()->route('training.evaluasilevel1.index')
                ->with('error', 'Anda belum hadir di pelatihan ini, tidak dapat mengisi evaluasi.');
        }

        return view('pages.training.evaluasi1.form', compact('pelatihan', 'presenters'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'ringkasan' => 'required|string',
            'materi' => 'required|array',
            'ide_saran' => 'required|string',
            'penyelenggaraan' => 'required|array',
            'sarana' => 'required|array',
            'instruktur' => 'required|array',
            'komentar' => 'required|string',
        ]);


        EvaluasiLevel1::updateOrCreate(
            [
                'pelatihan_id' => $id,
                'user_id' => auth()->id()
            ],
            [
                'ringkasan' => $request->ringkasan,
                'materi' => json_encode($request->materi),
                'penyelenggaraan' => json_encode($request->penyelenggaraan),
                'sarana' => json_encode($request->sarana),
                'instruktur' => json_encode($request->instruktur),
                'ide_saran' => $request->ide_saran,
                'komentar' => $request->komentar,
            ]
        );

        return redirect()->route('training.evaluasilevel1.index')->with('success', 'Evaluasi berhasil dikirim.');
    }

    public function show($id)
    {
        $evaluasi = EvaluasiLevel1::where('pelatihan_id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pelatihan = SuratPengajuanPelatihan::findOrFail($id);

        return view('pages.training.evaluasi1.show', compact('evaluasi', 'pelatihan'));
    }
}
