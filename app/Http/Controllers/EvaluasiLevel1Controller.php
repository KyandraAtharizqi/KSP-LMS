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

        // Get the participant record with relationships
        $participant = $pelatihan->participants()
            ->where('user_id', $user->id)
            ->with(['user', 'jabatan', 'department', 'directorate', 'division', 'superior'])
            ->first();

        if (!$participant) {
            abort(403, 'Anda bukan peserta pelatihan ini.');
        }

        // Check if evaluation exists and is already submitted
        $existing = EvaluasiLevel1::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->is_submitted) {
            return redirect()->route('training.evaluasilevel1.show', $pelatihan->id)
                ->with('info', 'Anda sudah mengisi evaluasi untuk pelatihan ini.');
        }

        // Get all presenters/instrukturs from pelatihan_presenters table for this training
        $presenters = PelatihanPresenter::with(['user', 'presenter'])
            ->where('pelatihan_id', $pelatihan->id)
            ->orderBy('date')
            ->orderBy('created_at')
            ->get();

        return view('pages.training.evaluasilevel1.form', compact('pelatihan', 'participant', 'presenters'));
    }

    public function store(Request $request, SuratPengajuanPelatihan $pelatihan)
    {
        try {
            // Add logging to see what data is being received
            \Log::info('Received request data: ', $request->all());
            
            $validationRules = [
                'user_id' => 'required|exists:users,id',
                'pelatihan_id' => 'required|exists:surat_pengajuan_pelatihans,id',
                'registration_id' => 'required|string',
                'kode_pelatihan' => 'required|string',
                'superior_id' => 'nullable|exists:users,id',
                'ringkasan_isi_materi' => 'required|string',
                'ide_saran_pengembangan' => 'required|string',
                'komplain_saran_masukan' => 'required|string',
                'materi_sistematika' => 'required|integer|min:1|max:5',
                'materi_pemahaman' => 'required|integer|min:1|max:5',
                'materi_pengetahuan' => 'required|integer|min:1|max:5',
                'materi_manfaat' => 'required|integer|min:1|max:5',
                'materi_tujuan' => 'required|integer|min:1|max:5',
                'penyelenggaraan_pengelolaan' => 'required|integer|min:1|max:5',
                'penyelenggaraan_jadwal' => 'required|integer|min:1|max:5',
                'penyelenggaraan_persiapan' => 'required|integer|min:1|max:5',
                'penyelenggaraan_pelayanan' => 'required|integer|min:1|max:5',
                'penyelenggaraan_koordinasi' => 'required|integer|min:1|max:5',
                'sarana_media' => 'required|integer|min:1|max:5',
                'sarana_kit' => 'required|integer|min:1|max:5',
                'sarana_kenyamanan' => 'required|integer|min:1|max:5',
                'sarana_kesesuaian' => 'required|integer|min:1|max:5',
                'sarana_belajar' => 'required|integer|min:1|max:5',
                'instrukturs' => 'required|array|min:1',
                'instrukturs.*.type' => 'required|in:internal,external',
                'instrukturs.*.user_id' => 'required_if:instrukturs.*.type,internal|exists:users,id',
                'instrukturs.*.presenter_id' => 'required_if:instrukturs.*.type,external|exists:presenters,id',
                'instrukturs.*.instruktur_penguasaan' => 'required|integer|min:1|max:5',
                'instrukturs.*.instruktur_teknik' => 'required|integer|min:1|max:5',
                'instrukturs.*.instruktur_sistematika' => 'required|integer|min:1|max:5',
                'instrukturs.*.instruktur_waktu' => 'required|integer|min:1|max:5',
                'instrukturs.*.instruktur_proses' => 'required|integer|min:1|max:5',
            ];

            $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        // Single transaction for everything
        DB::beginTransaction();

        try {
            // Find existing evaluation record
            $evaluasi = EvaluasiLevel1::where('pelatihan_id', $pelatihan->id)
                ->where('user_id', $request->user_id)
                ->first();

            \Log::info('Looking for existing evaluation: pelatihan_id=' . $pelatihan->id . ', user_id=' . $request->user_id);
            
            if (!$evaluasi) {
                \Log::error('No existing evaluation found');
                throw new \Exception('Evaluasi record tidak ditemukan. Pastikan Anda sudah mengisi daftar hadir.');
            }

            \Log::info('Found existing evaluation with ID: ' . $evaluasi->id);

            // Update the main record with evaluation data
            $evaluasi->ringkasan_isi_materi = $request->ringkasan_isi_materi;
            $evaluasi->ide_saran_pengembangan = $request->ide_saran_pengembangan;
            $evaluasi->komplain_saran_masukan = $request->komplain_saran_masukan;
            $evaluasi->is_submitted = true;
            $evaluasi->save();

            \Log::info('Main evaluation record updated successfully');

            // Create/update materi record
            $materiData = [
                'evaluasi_level_1_id' => $evaluasi->id,
                'materi_sistematika' => $request->materi_sistematika,
                'materi_pemahaman' => $request->materi_pemahaman,
                'materi_pengetahuan' => $request->materi_pengetahuan,
                'materi_manfaat' => $request->materi_manfaat,
                'materi_tujuan' => $request->materi_tujuan,
            ];
            
            \Log::info('Creating materi record with data: ', $materiData);
            
            // Use direct creation instead of updateOrCreate to see exact error
            $existingMateri = EvaluasiLevel1Materi::where('evaluasi_level_1_id', $evaluasi->id)->first();
            if ($existingMateri) {
                $existingMateri->update($materiData);
                \Log::info('Updated existing materi record');
            } else {
                EvaluasiLevel1Materi::create($materiData);
                \Log::info('Created new materi record');
            }

            // Create/update penyelenggaraan record
            $penyelenggaraanData = [
                'evaluasi_level_1_id' => $evaluasi->id,
                'penyelenggaraan_pengelolaan' => $request->penyelenggaraan_pengelolaan,
                'penyelenggaraan_jadwal' => $request->penyelenggaraan_jadwal,
                'penyelenggaraan_persiapan' => $request->penyelenggaraan_persiapan,
                'penyelenggaraan_pelayanan' => $request->penyelenggaraan_pelayanan,
                'penyelenggaraan_koordinasi' => $request->penyelenggaraan_koordinasi,
            ];
            
            $existingPenyelenggaraan = EvaluasiLevel1Penyelenggaraan::where('evaluasi_level_1_id', $evaluasi->id)->first();
            if ($existingPenyelenggaraan) {
                $existingPenyelenggaraan->update($penyelenggaraanData);
                \Log::info('Updated existing penyelenggaraan record');
            } else {
                EvaluasiLevel1Penyelenggaraan::create($penyelenggaraanData);
                \Log::info('Created new penyelenggaraan record');
            }

            // Create/update sarana record
            $saranaData = [
                'evaluasi_level_1_id' => $evaluasi->id,
                'sarana_media' => $request->sarana_media,
                'sarana_kit' => $request->sarana_kit,
                'sarana_kenyamanan' => $request->sarana_kenyamanan,
                'sarana_kesesuaian' => $request->sarana_kesesuaian,
                'sarana_belajar' => $request->sarana_belajar,
            ];
            
            $existingSarana = EvaluasiLevel1Sarana::where('evaluasi_level_1_id', $evaluasi->id)->first();
            if ($existingSarana) {
                $existingSarana->update($saranaData);
                \Log::info('Updated existing sarana record');
            } else {
                EvaluasiLevel1Sarana::create($saranaData);
                \Log::info('Created new sarana record');
            }

            // Handle instructor records
            EvaluasiLevel1Instruktur::where('evaluasi_level_1_id', $evaluasi->id)->delete();

            if ($request->has('instrukturs') && is_array($request->instrukturs)) {
                foreach ($request->instrukturs as $index => $instruktur) {
                    \Log::info('Processing instructor: ', ['index' => $index, 'data' => $instruktur]);
                    
                    $instrukturData = [
                        'evaluasi_level_1_id' => $evaluasi->id,
                        'type' => $instruktur['type'],
                        'user_id' => $instruktur['type'] === 'internal' ? ($instruktur['user_id'] ?? null) : null,
                        'presenter_id' => $instruktur['type'] === 'external' ? ($instruktur['presenter_id'] ?? null) : null,
                        'instruktur_penguasaan' => $instruktur['instruktur_penguasaan'],
                        'instruktur_teknik' => $instruktur['instruktur_teknik'],
                        'instruktur_sistematika' => $instruktur['instruktur_sistematika'],
                        'instruktur_waktu' => $instruktur['instruktur_waktu'],
                        'instruktur_proses' => $instruktur['instruktur_proses'],
                    ];
                    
                    EvaluasiLevel1Instruktur::create($instrukturData);
                    \Log::info('Instructor record created successfully');
                }
            }

            DB::commit();
            \Log::info('All records successfully saved for pelatihan: ' . $pelatihan->id);
            
            return redirect()->route('training.evaluasilevel1.index')->with('success', 'Evaluasi berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving evaluation: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan evaluasi: ' . $e->getMessage()])->withInput();
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