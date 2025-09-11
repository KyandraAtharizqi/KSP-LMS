<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\SuratPengajuanPelatihan;
use App\Models\DaftarHadirPelatihan;
use App\Models\DaftarHadirPelatihanStatus;
use App\Models\EvaluasiLevel1;
use App\Models\EvaluasiLevel1Materi;
use App\Models\EvaluasiLevel1Sarana;
use App\Models\EvaluasiLevel1Penyelenggaraan;
use App\Models\EvaluasiLevel1Instruktur;
use App\Models\EvaluasiLevel3Peserta;
use App\Models\PelatihanPresenter;
use App\Models\PelatihanLog;
use App\Models\User; 
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluasiLevel1Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'department_admin'])) {
            $pelatihans = SuratPengajuanPelatihan::with(['evaluasiLevel1', 'daftarHadirStatus', 'participants'])->get();
        } else {
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

    public function show(SuratPengajuanPelatihan $pelatihan, User $user = null)
    {
        $currentUser = Auth::user();

        // Determine which user's evaluation to show
        if (!in_array($currentUser->role, ['admin', 'department_admin'])) {
            $targetUserId = $currentUser->id;
            $participant = $pelatihan->participants()->where('user_id', $targetUserId)->first();
            if (!$participant) {
                abort(403, 'Anda bukan peserta pelatihan ini.');
            }
        } else {
            $targetUserId = $user ? $user->id : $currentUser->id;
        }

        $evaluasi = EvaluasiLevel1::with([
            'materi', 'penyelenggaraan', 'sarana',
            'instrukturs.user', 'instrukturs.presenter', 'user', 'superior'
        ])->where('pelatihan_id', $pelatihan->id)
        ->where('user_id', $targetUserId)
        ->first();

        if (!$evaluasi) {
            abort(404, 'Evaluasi tidak ditemukan untuk pelatihan ini.');
        }

        return view('pages.training.evaluasilevel1.show', compact('pelatihan', 'evaluasi'));
    }


    public function create(SuratPengajuanPelatihan $pelatihan)
    {
        $user = Auth::user();

        $participant = $pelatihan->participants()
            ->where('user_id', $user->id)
            ->with(['user', 'jabatan', 'department', 'directorate', 'division', 'superior'])
            ->first();

        if (!$participant) {
            abort(403, 'Anda bukan peserta pelatihan ini.');
        }

        $existing = EvaluasiLevel1::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->is_submitted) {
            return redirect()->route('training.evaluasilevel1.show', $pelatihan->id)
                ->with('info', 'Anda sudah mengisi evaluasi untuk pelatihan ini.');
        }

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

    DB::beginTransaction();

    try {
        $evaluasi = EvaluasiLevel1::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$evaluasi) {
            throw new \Exception('Evaluasi record tidak ditemukan. Pastikan Anda sudah mengisi daftar hadir.');
        }

        $evaluasi->ringkasan_isi_materi = $request->ringkasan_isi_materi;
        $evaluasi->ide_saran_pengembangan = $request->ide_saran_pengembangan;
        $evaluasi->komplain_saran_masukan = $request->komplain_saran_masukan;
        $evaluasi->is_submitted = true;
        $evaluasi->save();

        $existingEval3 = EvaluasiLevel3Peserta::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$existingEval3) {
            EvaluasiLevel3Peserta::create([
                'pelatihan_id' => $pelatihan->id,
                'user_id' => $request->user_id,
                'registration_id' => $request->registration_id,
                'kode_pelatihan' => $request->kode_pelatihan,
                'manfaat_pelatihan' => null,
                'kinerja' => null,
                'saran' => null,
                'is_submitted' => 0,
                'is_accepted' => 0,
            ]);
        }

        // Materi
        $materiData = [
            'evaluasi_level_1_id' => $evaluasi->id,
            'materi_sistematika' => $request->materi_sistematika,
            'materi_pemahaman' => $request->materi_pemahaman,
            'materi_pengetahuan' => $request->materi_pengetahuan,
            'materi_manfaat' => $request->materi_manfaat,
            'materi_tujuan' => $request->materi_tujuan,
        ];
        EvaluasiLevel1Materi::updateOrCreate(['evaluasi_level_1_id' => $evaluasi->id], $materiData);

        // Penyelenggaraan
        $penyelenggaraanData = [
            'evaluasi_level_1_id' => $evaluasi->id,
            'penyelenggaraan_pengelolaan' => $request->penyelenggaraan_pengelolaan,
            'penyelenggaraan_jadwal' => $request->penyelenggaraan_jadwal,
            'penyelenggaraan_persiapan' => $request->penyelenggaraan_persiapan,
            'penyelenggaraan_pelayanan' => $request->penyelenggaraan_pelayanan,
            'penyelenggaraan_koordinasi' => $request->penyelenggaraan_koordinasi,
        ];
        EvaluasiLevel1Penyelenggaraan::updateOrCreate(['evaluasi_level_1_id' => $evaluasi->id], $penyelenggaraanData);

        // Sarana
        $saranaData = [
            'evaluasi_level_1_id' => $evaluasi->id,
            'sarana_media' => $request->sarana_media,
            'sarana_kit' => $request->sarana_kit,
            'sarana_kenyamanan' => $request->sarana_kenyamanan,
            'sarana_kesesuaian' => $request->sarana_kesesuaian,
            'sarana_belajar' => $request->sarana_belajar,
        ];
        EvaluasiLevel1Sarana::updateOrCreate(['evaluasi_level_1_id' => $evaluasi->id], $saranaData);

        // Instrukturs
        EvaluasiLevel1Instruktur::where('evaluasi_level_1_id', $evaluasi->id)->delete();
        if ($request->has('instrukturs')) {
            foreach ($request->instrukturs as $instruktur) {
                EvaluasiLevel1Instruktur::create([
                    'evaluasi_level_1_id' => $evaluasi->id,
                    'type' => $instruktur['type'],
                    'user_id' => $instruktur['type'] === 'internal' ? ($instruktur['user_id'] ?? null) : null,
                    'presenter_id' => $instruktur['type'] === 'external' ? ($instruktur['presenter_id'] ?? null) : null,
                    'instruktur_penguasaan' => $instruktur['instruktur_penguasaan'],
                    'instruktur_teknik' => $instruktur['instruktur_teknik'],
                    'instruktur_sistematika' => $instruktur['instruktur_sistematika'],
                    'instruktur_waktu' => $instruktur['instruktur_waktu'],
                    'instruktur_proses' => $instruktur['instruktur_proses'],
                ]);
            }
        }

        // ---------------- Pelatihan Log ----------------
        $daftarHadirRecords = DaftarHadirPelatihan::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $request->user_id)
            ->get();

        $participantSnapshot = $pelatihan->participants()
            ->where('user_id', $request->user_id)
            ->first();

        $userLive = User::with(['department', 'jabatan'])
            ->find($request->user_id);

        foreach ($daftarHadirRecords as $dh) {
            $jam = ($dh->check_in_time && $dh->check_out_time)
                ? round((strtotime($dh->check_out_time) - strtotime($dh->check_in_time)) / 3600, 2)
                : 0;

            PelatihanLog::updateOrCreate(
                [
                    'pelatihan_id' => $pelatihan->id,
                    'user_id' => $request->user_id,
                    'tanggal' => $dh->date,
                ],
                [
                    'kode_pelatihan' => $pelatihan->kode_pelatihan,
                    'registration_id' => $request->registration_id,

                    // Pengajuan snapshot
                    'pengajuan_department_id' => $participantSnapshot?->department?->name 
                        ?? $participantSnapshot?->department_id,
                    'pengajuan_jabatan_full' => $participantSnapshot?->jabatan_full
                        ?? ($participantSnapshot?->jabatan_id
                            ? Jabatan::find($participantSnapshot->jabatan_id)?->name
                            : null),

                    // Current (live user)
                    'current_department_id' => $request->user()?->department?->name 
                    ?? $request->user()?->department_id,
                    'current_jabatan_full' => $userLive?->jabatan?->name,

                    'jam' => $jam,
                ]
            );
        }

        DB::commit();
        return redirect()->route('training.evaluasilevel1.index')
            ->with('success', 'Evaluasi berhasil disimpan.');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Error saving evaluation: ' . $e->getMessage());
        \Log::error('Trace: ' . $e->getTraceAsString());
        return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan evaluasi: ' . $e->getMessage()])->withInput();
    }
}



    public function pdfView(SuratPengajuanPelatihan $pelatihan, User $user = null)
    {
        $currentUser = Auth::user();
        
        // For ordinary users, always use their own ID regardless of the $user parameter
        if (!in_array($currentUser->role, ['admin', 'department_admin'])) {
            $targetUserId = $currentUser->id;
            
            // Also verify that the current user is actually a participant in this training
            $participant = $pelatihan->participants()
                ->where('user_id', $currentUser->id)
                ->first();
                
            if (!$participant) {
                abort(403, 'Anda bukan peserta pelatihan ini.');
            }
        } else {
            // For admins, use the provided user ID or default to current user
            $targetUserId = $user ? $user->id : $currentUser->id;
        }

        $evaluasi = EvaluasiLevel1::with([
            'materi', 'penyelenggaraan', 'sarana',
            'instrukturs.user', 'instrukturs.presenter', 'user'
        ])->where('pelatihan_id', $pelatihan->id)
        ->where('user_id', $targetUserId)
        ->first(); // Use first() instead of firstOrFail() initially

        // Check if evaluation exists
        if (!$evaluasi) {
            abort(404, 'Evaluasi tidak ditemukan untuk pelatihan ini.');
        }
        
        // Check if evaluation is submitted (optional - you might want this check)
        if (!$evaluasi->is_submitted) {
            abort(403, 'Evaluasi belum disubmit dan tidak dapat diunduh.');
        }
        
        // Get user signature if available
        $signature = null;
        if ($evaluasi->user && $evaluasi->user->registration_id) {
            $signatureRecord = \App\Models\SignatureAndParaf::where('registration_id', $evaluasi->user->registration_id)
                ->first();
            
            if ($signatureRecord && $signatureRecord->signature_path) {
                $signaturePath = public_path('storage/' . ltrim($signatureRecord->signature_path, '/'));
                $signature = file_exists($signaturePath) ? $signaturePath : null;
            }
        }

        $pdf = Pdf::loadView('pages.training.evaluasilevel1.pdf_view', compact('pelatihan', 'evaluasi', 'signature'));

        $filename = 'Evaluasi_Level_1_' . $pelatihan->kode_pelatihan;
        if ($user && in_array($currentUser->role, ['admin', 'department_admin'])) {
            $filename .= '_' . str_replace(' ', '_', $evaluasi->user->name);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
    public function updateSuperior(Request $request, EvaluasiLevel1 $evaluasi)
    {
        $user = auth()->user();
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
