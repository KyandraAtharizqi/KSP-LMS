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
use App\Models\EvaluasiLevel3Peserta;
use App\Models\PelatihanPresenter;
use App\Models\User; 
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluasiLevel1Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'department_admin'])) {
            // Admin & DeptAdmin can see all evaluations
            $pelatihans = SuratPengajuanPelatihan::with(['evaluasiLevel1', 'daftarHadirStatus', 'participants'])->get();
        } else {
            // Normal participants only see their own
            $pelatihans = SuratPengajuanPelatihan::whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereDoesntHave('daftarHadirStatus', function ($q) {
                    $q->where('is_submitted', false);
                })
                ->with(['evaluasiLevel1', 'daftarHadirStatus', 'participants'])
                ->get();
        }

        $availableSuperiors = User::all();

        return view('pages.training.evaluasilevel1.index', compact('pelatihans', 'availableSuperiors'));
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

            /**
             * ✅ Insert initial record into EvaluasiLevel3
             * Only if not already exists
             */
            $existingEval3 = EvaluasiLevel3Peserta::where('pelatihan_id', $pelatihan->id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$existingEval3) {
                EvaluasiLevel3Peserta::create([
                    'pelatihan_id' => $pelatihan->id,
                    'user_id' => $request->user_id,
                    'registration_id' => $request->registration_id,
                    'kode_pelatihan' => $request->kode_pelatihan,
                    'manfaat_pelatihan' => null, // to be filled later
                    'kinerja' => null,           // to be filled later
                    'saran' => null,             // to be filled later
                    'is_submitted' => 0,         // explicitly set to 0 (false)
                    'is_accepted' => 0,          // explicitly set to 0 (false)
                ]);
                \Log::info('Initial Evaluasi Level 3 record created for user_id=' . $request->user_id);
            } else {
                \Log::info('Evaluasi Level 3 record already exists, skipping creation');
            }

            // Create/update materi record
            $materiData = [
                'evaluasi_level_1_id' => $evaluasi->id,
                'materi_sistematika' => $request->materi_sistematika,
                'materi_pemahaman' => $request->materi_pemahaman,
                'materi_pengetahuan' => $request->materi_pengetahuan,
                'materi_manfaat' => $request->materi_manfaat,
                'materi_tujuan' => $request->materi_tujuan,
            ];
            
            $existingMateri = EvaluasiLevel1Materi::where('evaluasi_level_1_id', $evaluasi->id)->first();
            if ($existingMateri) {
                $existingMateri->update($materiData);
            } else {
                EvaluasiLevel1Materi::create($materiData);
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
            } else {
                EvaluasiLevel1Penyelenggaraan::create($penyelenggaraanData);
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
            } else {
                EvaluasiLevel1Sarana::create($saranaData);
            }

            // Handle instructor records
            EvaluasiLevel1Instruktur::where('evaluasi_level_1_id', $evaluasi->id)->delete();

            if ($request->has('instrukturs') && is_array($request->instrukturs)) {
                foreach ($request->instrukturs as $instruktur) {
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
                }
            }

            DB::commit();
            \Log::info('All records successfully saved for pelatihan: ' . $pelatihan->id);
            
            return redirect()->route('training.evaluasilevel1.index')
                ->with('success', 'Evaluasi berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving evaluation: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan evaluasi: ' . $e->getMessage()])->withInput();
        }
    }


    public function show(SuratPengajuanPelatihan $pelatihan)
    {
        $user = Auth::user();

        $query = EvaluasiLevel1::with([
            'materi', 'penyelenggaraan', 'sarana',
            'instrukturs.user', 'instrukturs.presenter'
        ])->where('pelatihan_id', $pelatihan->id);

        if (!in_array($user->role, ['admin', 'department_admin'])) {
            $query->where('user_id', $user->id);
        }

        $evaluasi = $query->firstOrFail();

        return view('pages.training.evaluasilevel1.show', compact('pelatihan', 'evaluasi'));
    }

    public function pdfView(SuratPengajuanPelatihan $pelatihan)
    {
        $user = Auth::user();

        $query = EvaluasiLevel1::with([
            'materi', 'penyelenggaraan', 'sarana',
            'instrukturs.user', 'instrukturs.presenter'
        ])->where('pelatihan_id', $pelatihan->id);

        if (!in_array($user->role, ['admin', 'department_admin'])) {
            $query->where('user_id', $user->id);
        }

        $evaluasi = $query->firstOrFail();

        $pdf = Pdf::loadView('pages.training.evaluasilevel1.pdf_view', [
            'pelatihan' => $pelatihan,
            'evaluasi' => $evaluasi
        ]);

        return $pdf->download('Evaluasi_Level_1_' . $pelatihan->kode_pelatihan . '.pdf');
    }


    public function updateSuperior(Request $request, EvaluasiLevel1 $evaluasi)
    {
        $user = auth()->user();

        // Optional: ensure the logged-in user can only change their own superior
        if ($evaluasi->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengubah superior ini.');
        }

        $request->validate([
            'superior_id' => 'nullable|exists:users,id',
        ]);

        $evaluasi->superior_id = $request->superior_id;
        $evaluasi->save();

        return redirect()->back()->with('success', 'Superior berhasil diperbarui.');
    }

}