<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\SuratPengajuanPelatihan;
use App\Models\DaftarHadirPelatihanStatus;
use App\Models\EvaluasiLevel1;
use App\Models\EvaluasiLevel1Materi;
use App\Models\EvaluasiLevel1Sarana;
use App\Models\EvaluasiLevel1Penyelenggaraan;
use App\Models\EvaluasiLevel1Instruktur;
use App\Models\PelatihanPresenter;

class EvaluasiLevel1Controller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $pelatihans = SuratPengajuanPelatihan::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->whereDoesntHave('daftarHadirStatus', function ($q) {
            $q->where('is_submitted', false);
        })
        ->with(['evaluasiLevel1', 'daftarHadirStatus'])
        ->get();

        return view('pages.training.evaluasilevel1.index', compact('pelatihans'));
    }

    public function create(SuratPengajuanPelatihan $pelatihan)
    {
        $user = Auth::user();

        $participant = $pelatihan->participants()
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            abort(403, 'Anda bukan peserta pelatihan ini.');
        }

        $existing = EvaluasiLevel1::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->route('training.evaluasilevel1.show', $pelatihan->id)
                ->with('info', 'Anda sudah mengisi evaluasi untuk pelatihan ini.');
        }

        $registration_id = $participant->registration_id;

        // Get presenters/instrukturs from pelatihan_presenters table
        $presenters = PelatihanPresenter::with(['user', 'presenter'])
            ->where('pelatihan_id', $pelatihan->id)
            ->orderBy('date')
            ->get();

        return view('pages.training.evaluasilevel1.form', compact('pelatihan', 'registration_id', 'presenters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelatihan_id' => 'required|exists:surat_pengajuan_pelatihans,id',
            'user_id' => 'required|exists:users,id',
            'ringkasan_isi_materi' => 'required|string',
            'ide_saran_pengembangan' => 'required|string',
            'komplain_saran_masukan' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $evaluasi = EvaluasiLevel1::create([
                'pelatihan_id' => $request->pelatihan_id,
                'user_id' => $request->user_id,
                'registration_id' => $request->registration_id,
                'kode_pelatihan' => $request->kode_pelatihan,
                'nama_pelatihan' => $request->nama_pelatihan,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'tempat' => $request->tempat,
                'name' => $request->name,
                'department' => $request->department,
                'jabatan_full' => $request->jabatan_full,
                'superior_id' => $request->superior_id,
                'ringkasan_isi_materi' => $request->ringkasan_isi_materi,
                'ide_saran_pengembangan' => $request->ide_saran_pengembangan,
                'komplain_saran_masukan' => $request->komplain_saran_masukan,
                'is_submitted' => true,
            ]);

            // Add the materi relationship
            EvaluasiLevel1Materi::create([
                'evaluasi_level_1_id' => $evaluasi->id,
                'materi_sistematika' => $request->materi_sistematika,
                'materi_pemahaman' => $request->materi_pemahaman,
                'materi_pengetahuan' => $request->materi_pengetahuan,
                'materi_manfaat' => $request->materi_manfaat,
                'materi_tujuan' => $request->materi_tujuan,
            ]);

            EvaluasiLevel1Penyelenggaraan::create([
                'evaluasi_level_1_id' => $evaluasi->id,
                'penyelenggaraan_pengelolaan' => $request->penyelenggaraan_pengelolaan,
                'penyelenggaraan_jadwal' => $request->penyelenggaraan_jadwal,
                'penyelenggaraan_persiapan' => $request->penyelenggaraan_persiapan,
                'penyelenggaraan_pelayanan' => $request->penyelenggaraan_pelayanan,
                'penyelenggaraan_koordinasi' => $request->penyelenggaraan_koordinasi,
            ]);

            EvaluasiLevel1Sarana::create([
                'evaluasi_level_1_id' => $evaluasi->id,
                'sarana_media' => $request->sarana_media,
                'sarana_kit' => $request->sarana_kit,
                'sarana_kenyamanan' => $request->sarana_kenyamanan,
                'sarana_kesesuaian' => $request->sarana_kesesuaian,
                'sarana_belajar' => $request->sarana_belajar,
            ]);

            if ($request->has('instrukturs')) {
                foreach ($request->instrukturs as $instruktur) {
                    EvaluasiLevel1Instruktur::create([
                        'evaluasi_level_1_id' => $evaluasi->id,
                        'type' => $instruktur['type'],
                        'user_id' => $instruktur['type'] === 'internal' ? ($instruktur['user_id'] ?? null) : null,
                        'presenter_id' => $instruktur['type'] === 'external' ? $instruktur['presenter_id'] : null,
                        'instruktur_penguasaan' => $instruktur['instruktur_penguasaan'],
                        'instruktur_teknik' => $instruktur['instruktur_teknik'],
                        'instruktur_sistematika' => $instruktur['instruktur_sistematika'],
                        'instruktur_waktu' => $instruktur['instruktur_waktu'],
                        'instruktur_proses' => $instruktur['instruktur_proses'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('training.evaluasilevel1.index')->with('success', 'Evaluasi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan evaluasi: ' . $e->getMessage()]);
        }
    }

    public function show(SuratPengajuanPelatihan $pelatihan)
    {
        $user = auth()->user();

        $evaluasi = EvaluasiLevel1::with([
            'materi',
            'penyelenggaraan', 
            'sarana', 
            'instrukturs.user',
            'instrukturs.presenter'
        ])
        ->where('pelatihan_id', $pelatihan->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

        return view('pages.training.evaluasilevel1.show', compact('pelatihan', 'evaluasi'));
    }
}