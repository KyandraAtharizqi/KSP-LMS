<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratPengajuanPelatihan;
use App\Models\EvaluasiLevel3Peserta;
use App\Models\User;
use App\Models\EvaluasiLevel3Signature;
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluasiLevel3PesertaController extends Controller
{
    // Index: list all pelatihan available for the logged-in peserta
    public function index()
    {
        $userId = Auth::id();

        $pelatihans = SuratPengajuanPelatihan::whereHas('participants', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();

        return view('pages.training.evaluasilevel3.peserta.index', compact('pelatihans'));
    }

    // Create: show form for a pelatihan
    public function create(SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();

        // Check if the user is a participant
        if (!$pelatihan->participants->contains('user_id', $userId)) {
            abort(403, 'Anda tidak berhak mengisi evaluasi untuk pelatihan ini.');
        }

        return view('pages.training.evaluasilevel3.peserta.create', compact('pelatihan'));
    }

    // Store: save submitted evaluation
    public function store(Request $request, SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();

        if (!$pelatihan->participants->contains('user_id', $userId)) {
            abort(403, 'Anda tidak berhak mengisi evaluasi untuk pelatihan ini.');
        }

        $request->validate([
            'action_plan.*' => 'required|string',
            'frekuensi.*' => 'required|integer|min:0|max:3',
            'hasil.*' => 'required|integer|min:1|max:4',
            'manfaat_pelatihan' => 'nullable|string',
            'kinerja' => 'required|integer|min:0|max:2',
            'saran' => 'nullable|string',
        ]);

        $evaluasi = EvaluasiLevel3Peserta::updateOrCreate(
            [
                'pelatihan_id' => $pelatihan->id,
                'user_id' => $userId,
            ],
            [
                'registration_id' => $pelatihan->participants->firstWhere('user_id', $userId)->registration_id,
                'kode_pelatihan' => $pelatihan->kode_pelatihan,
                'manfaat_pelatihan' => $request->manfaat_pelatihan,
                'kinerja' => $request->kinerja,
                'saran' => $request->saran,
            ]
        );

        // 🔥 Reset then insert action plans
        $evaluasi->actionPlans()->delete();
        if ($request->has('action_plan')) {
            foreach ($request->action_plan as $index => $plan) {
                $evaluasi->actionPlans()->create([
                    'evaluasi_level_3_peserta_id' => $evaluasi->id, // ✅ correct FK
                    'pelatihan_id' => $pelatihan->id,
                    'user_id' => $userId,
                    'registration_id' => $pelatihan->participants->firstWhere('user_id', $userId)->registration_id,
                    'kode_pelatihan' => $pelatihan->kode_pelatihan,
                    'action_plan' => $plan,
                    'diaplikasikan' => isset($request->diaplikasikan[$index]) ? 1 : 0,
                    'frekuensi' => $request->frekuensi[$index],
                    'hasil' => $request->hasil[$index],
                ]);
            }
        }

        // 🔥 Reset then insert feedback
        $evaluasi->feedbacks()->delete();
        $evaluasi->feedbacks()->create([
            'evaluasi_level_3_peserta_id' => $evaluasi->id, // ✅ correct FK
            'pelatihan_id' => $pelatihan->id,
            'user_id' => $userId,
            'registration_id' => $pelatihan->participants->firstWhere('user_id', $userId)->registration_id,
            'kode_pelatihan' => $pelatihan->kode_pelatihan,
            'telah_mampu' => $request->has('telah_mampu') ? 1 : 0,
            'membantu_mengaplikasikan' => $request->has('membantu_mengaplikasikan') ? 1 : 0,
            'tidak_diaplikasikan_karena' => $request->tidak_diaplikasikan_karena,
            'memberikan_informasi_mengenai' => $request->memberikan_informasi_mengenai,
            'lain_lain' => $request->lain_lain,
        ]);

        // 🔥 Create a new signature entry for superior
        $superior = User::find(Auth::user()->superior_id);
        if ($superior) {
            $lastRound = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasi->id)->max('round') ?? 0;

            EvaluasiLevel3Signature::create([
                'evaluasi_level_3_peserta_id' => $evaluasi->id, // ✅ correct FK
                'pelatihan_id' => $pelatihan->id,
                'approver_id' => $superior->id,
                'registration_id' => $evaluasi->registration_id,
                'kode_pelatihan' => $evaluasi->kode_pelatihan,
                'round' => $lastRound + 1,
                'status' => 'pending',
                'jabatan_id' => $superior->jabatan_id,
                'jabatan_full' => $superior->jabatan_full,
                'department_id' => $superior->department_id,
                'directorate_id' => $superior->directorate_id,
                'division_id' => $superior->division_id,
                'golongan' => $superior->golongan,
            ]);
        }

        return redirect()->route('evaluasi-level-3.peserta.index')
            ->with('success', 'Evaluasi Level 3 berhasil disimpan & menunggu persetujuan atasan.');
    }

    // Preview: show saved evaluation
    public function preview(SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();

        $evaluasi = EvaluasiLevel3Peserta::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        return view('pages.training.evaluasilevel3.peserta.preview', compact('evaluasi'));
    }

    // Edit: show form with existing evaluation data
    public function edit(SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();

        $evaluasi = EvaluasiLevel3Peserta::with(['actionPlans', 'feedbacks'])
            ->where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        return view('pages.training.evaluasilevel3.peserta.edit', compact('pelatihan', 'evaluasi'));
    }

    // Update: save changes to evaluation
    public function update(Request $request, SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();

        $evaluasi = EvaluasiLevel3Peserta::where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $request->validate([
            'action_plan.*' => 'required|string',
            'frekuensi.*' => 'required|integer|min:0|max:3',
            'hasil.*' => 'required|integer|min:1|max:4',
            'manfaat_pelatihan' => 'nullable|string',
            'kinerja' => 'required|integer|min:0|max:2',
            'saran' => 'nullable|string',
        ]);

        $evaluasi->update([
            'manfaat_pelatihan' => $request->manfaat_pelatihan,
            'kinerja' => $request->kinerja,
            'saran' => $request->saran,
        ]);

        // 🔥 Reset & update action plans
        $evaluasi->actionPlans()->delete();
        if ($request->has('action_plan')) {
            foreach ($request->action_plan as $index => $plan) {
                $evaluasi->actionPlans()->create([
                    'evaluasi_level_3_peserta_id' => $evaluasi->id, // ✅ correct FK
                    'pelatihan_id' => $pelatihan->id,
                    'user_id' => $userId,
                    'registration_id' => $pelatihan->participants->firstWhere('user_id', $userId)->registration_id,
                    'kode_pelatihan' => $pelatihan->kode_pelatihan,
                    'action_plan' => $plan,
                    'diaplikasikan' => isset($request->diaplikasikan[$index]) ? 1 : 0,
                    'frekuensi' => $request->frekuensi[$index],
                    'hasil' => $request->hasil[$index],
                ]);
            }
        }

        // 🔥 Reset & update feedback
        $evaluasi->feedbacks()->delete();
        $evaluasi->feedbacks()->create([
            'evaluasi_level_3_peserta_id' => $evaluasi->id, // ✅ correct FK
            'pelatihan_id' => $pelatihan->id,
            'user_id' => $userId,
            'registration_id' => $pelatihan->participants->firstWhere('user_id', $userId)->registration_id,
            'kode_pelatihan' => $pelatihan->kode_pelatihan,
            'telah_mampu' => $request->has('telah_mampu') ? 1 : 0,
            'membantu_mengaplikasikan' => $request->has('membantu_mengaplikasikan') ? 1 : 0,
            'tidak_diaplikasikan_karena' => $request->tidak_diaplikasikan_karena,
            'memberikan_informasi_mengenai' => $request->memberikan_informasi_mengenai,
            'lain_lain' => $request->lain_lain,
        ]);

        // 🔥 New round when resubmitted
        $superior = User::find(Auth::user()->superior_id);
        if ($superior) {
            $lastRound = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasi->id)->max('round') ?? 0;

            EvaluasiLevel3Signature::create([
                'evaluasi_level_3_peserta_id' => $evaluasi->id, // ✅ correct FK
                'pelatihan_id' => $pelatihan->id,
                'approver_id' => $superior->id,
                'registration_id' => $evaluasi->registration_id,
                'kode_pelatihan' => $evaluasi->kode_pelatihan,
                'round' => $lastRound + 1,
                'status' => 'pending',
                'jabatan_id' => $superior->jabatan_id,
                'jabatan_full' => $superior->jabatan_full,
                'department_id' => $superior->department_id,
                'directorate_id' => $superior->directorate_id,
                'division_id' => $superior->division_id,
                'golongan' => $superior->golongan,
            ]);
        }

        return redirect()->route('evaluasi-level-3.peserta.index')
            ->with('success', 'Evaluasi Level 3 berhasil diperbarui & dikirim ulang ke atasan.');
    }



    public function pdfView(SuratPengajuanPelatihan $pelatihan)
    {
        $user = auth()->user();

        $evaluasi = EvaluasiLevel3Peserta::with([
            'pelatihan',
            'user',
            'actionPlans',
            'feedbacks',
            'signatures.user.jabatan'
        ])
        ->where('pelatihan_id', $pelatihan->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

        $pdf = Pdf::loadView('pages.training.evaluasilevel3.peserta.pdf_view', [
            'pelatihan' => $pelatihan,
            'evaluasi' => $evaluasi,
        ]);

        return $pdf->download("Evaluasi_Level_3_{$pelatihan->kode_pelatihan}.pdf");
    }



}
