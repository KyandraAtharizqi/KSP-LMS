<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SuratPengajuanPelatihan;
use App\Models\EvaluasiLevel3Peserta;
use App\Models\User;
use App\Models\EvaluasiLevel3Signature;
use App\Models\TrainingParticipant; // Add this line
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EvaluasiLevel3PesertaController extends Controller
{
    // Index: list all pelatihan available for the logged-in peserta
    public function index()
    {
        $user = auth()->user();

        $evaluasis = in_array($user->role, ['admin', 'department_admin'])
            ? EvaluasiLevel3Peserta::all()
            : EvaluasiLevel3Peserta::where('user_id', $user->id)->get();

        $evaluasisArray = [];

        foreach ($evaluasis as $evaluasi) {
            $pelatihan = DB::table('surat_pengajuan_pelatihans')
                ->select('id', 'kode_pelatihan', 'judul', 'penyelenggara')
                ->where('id', $evaluasi->pelatihan_id)
                ->first();

            $peserta = DB::table('users')
                ->select('id', 'name', 'superior_id')
                ->where('id', $evaluasi->user_id)
                ->first();

            $currentSuperiorName = $peserta && $peserta->superior_id
                ? DB::table('users')->where('id', $peserta->superior_id)->value('name')
                : null;

            $pengajuanSuperiorId = DB::table('training_participants')
                ->where('pelatihan_id', $evaluasi->pelatihan_id)
                ->where('user_id', $evaluasi->user_id)
                ->value('superior_id');

            $pengajuanSuperiorName = $pengajuanSuperiorId
                ? DB::table('users')->where('id', $pengajuanSuperiorId)->value('name')
                : null;

            $isForceOpened = $evaluasi->is_allowed == 1;

            if ($isForceOpened) {
                $isAllowed = true;
                $openDate = null;
                $daysRemaining = $hoursRemaining = $minutesRemaining = $secondsRemaining = 0;
            } else {
                $latestDate = DB::table('daftar_hadir_pelatihan_status')
                    ->where('pelatihan_id', $evaluasi->pelatihan_id)
                    ->where('is_submitted', true)
                    ->max('date');

                if ($latestDate) {
                    $openDate = Carbon::parse($latestDate)->addDays(30)->endOfDay();
                    $now = Carbon::now();

                    $isAllowed = $now->greaterThanOrEqualTo($openDate);
                    $daysRemaining = $hoursRemaining = $minutesRemaining = $secondsRemaining = 0;

                    if (!$isAllowed) {
                        $diff = $now->diff($openDate);
                        $daysRemaining = $diff->days;
                        $hoursRemaining = $diff->h;
                        $minutesRemaining = $diff->i;
                        $secondsRemaining = $diff->s;
                    }
                } else {
                    $openDate = null;
                    $isAllowed = false;
                    $daysRemaining = $hoursRemaining = $minutesRemaining = $secondsRemaining = null;
                }
            }

            $evaluasisArray[] = [
                'id' => $evaluasi->id,
                'pelatihan' => [
                    'id' => $pelatihan->id,
                    'kode_pelatihan' => $pelatihan->kode_pelatihan,
                    'judul' => $pelatihan->judul,
                    'penyelenggara' => $pelatihan->penyelenggara,
                ],
                'user' => [
                    'id' => $peserta->id,
                    'name' => $peserta->name,
                    'superior_id' => $currentSuperiorName,
                ],
                'is_allowed' => $isAllowed,
                'is_force_opened' => $isForceOpened,
                'countdown_target' => $openDate ? $openDate->toDateTimeString() : null,
                'days_remaining' => $daysRemaining,
                'hours_remaining' => $hoursRemaining,
                'minutes_remaining' => $minutesRemaining,
                'seconds_remaining' => $secondsRemaining,
                'pengajuan_superior_id' => $pengajuanSuperiorName,
                'is_submitted' => $evaluasi->is_submitted ?? false,
            ];
        }

        return view('pages.training.evaluasilevel3.peserta.index', [
            'evaluasis' => $evaluasisArray
        ]);
    }

    public function forceOpen(Request $request, $evaluasiId)
    {
        try {
            $user = auth()->user();
            if (!in_array($user->role, ['admin', 'department_admin'])) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            $evaluasi = EvaluasiLevel3Peserta::find($evaluasiId);
            if (!$evaluasi) {
                return response()->json(['error' => 'Evaluasi not found'], 404);
            }

            $evaluasi->is_allowed = 1;
            $evaluasi->save();

            return response()->json([
                'success' => true,
                'message' => 'Evaluasi berhasil dibuka paksa',
                'evaluasi_id' => $evaluasiId
            ]);

        } catch (\Exception $e) {
            \Log::error('Force open evaluasi error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function create(SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();
        if (!$pelatihan->participants->contains('user_id', $userId)) {
            abort(403, 'Anda tidak berhak mengisi evaluasi untuk pelatihan ini.');
        }

        return view('pages.training.evaluasilevel3.peserta.create', compact('pelatihan'));
    }

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
                'is_submitted' => 1,
                'submitted_at' => now(),
            ]
        );

        // Action Plans
        $evaluasi->actionPlans()->delete();
        if ($request->has('action_plan')) {
            foreach ($request->action_plan as $index => $plan) {
                $evaluasi->actionPlans()->create([
                    'evaluasi_level_3_peserta_id' => $evaluasi->id,
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

        // Feedback
        $evaluasi->feedbacks()->delete();
        $evaluasi->feedbacks()->create([
            'evaluasi_level_3_peserta_id' => $evaluasi->id,
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

        // Signature for superior
        $superior = User::find(Auth::user()->superior_id);
        if ($superior) {
            $lastRound = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasi->id)->max('round') ?? 0;
            EvaluasiLevel3Signature::create([
                'evaluasi_level_3_peserta_id' => $evaluasi->id,
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

    public function edit(SuratPengajuanPelatihan $pelatihan)
    {
        $userId = Auth::id();
        $evaluasi = EvaluasiLevel3Peserta::with(['actionPlans', 'feedbacks'])
            ->where('pelatihan_id', $pelatihan->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        return view('pages.training.evaluasilevel3.peserta.edit', compact('pelatihan', 'evaluasi'));
    }

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
            'is_submitted' => 1,
            'submitted_at' => now(),
        ]);

        $evaluasi->actionPlans()->delete();
        if ($request->has('action_plan')) {
            foreach ($request->action_plan as $index => $plan) {
                $evaluasi->actionPlans()->create([
                    'evaluasi_level_3_peserta_id' => $evaluasi->id,
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

        $evaluasi->feedbacks()->delete();
        $evaluasi->feedbacks()->create([
            'evaluasi_level_3_peserta_id' => $evaluasi->id,
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

        $superior = User::find(Auth::user()->superior_id);
        if ($superior) {
            $lastRound = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasi->id)->max('round') ?? 0;
            EvaluasiLevel3Signature::create([
                'evaluasi_level_3_peserta_id' => $evaluasi->id,
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

public function preview(EvaluasiLevel3Peserta $evaluasi)
{
    $user = Auth::user();

    // Authorization
    if (!in_array($user->role, ['admin', 'department_admin']) && $evaluasi->user_id !== $user->id) {
        abort(403, 'Anda tidak berhak mengakses evaluasi ini.');
    }

    return view('pages.training.evaluasilevel3.peserta.preview', compact('evaluasi'));
}

public function pdfView(EvaluasiLevel3Peserta $evaluasi)
{
    $user = Auth::user();

    if (!in_array($user->role, ['admin', 'department_admin']) && $evaluasi->user_id !== $user->id) {
        abort(403, 'Anda tidak berhak mengakses evaluasi ini.');
    }

    $evaluasi->load([
        'pelatihan',
        'user.superior', // Add superior relationship here
        'actionPlans',
        'feedbacks',
        'signatures.user.jabatan'
    ]);

    // Get the training participant record for this specific evaluation
    $trainingParticipant = TrainingParticipant::where('registration_id', $evaluasi->registration_id)
        ->where('pelatihan_id', $evaluasi->pelatihan_id)
        ->first();

    // Get jabatan saat pengajuan
    $jabatan_saat_pengajuan = $trainingParticipant ? $trainingParticipant->jabatan_full : '';

    // Get atasan saat pengajuan
    $atasan_saat_pengajuan = '';
    if ($trainingParticipant && $trainingParticipant->superior_id) {
        $superior = User::find($trainingParticipant->superior_id);
        $atasan_saat_pengajuan = $superior ? $superior->name : '';
    }

    $pelatihan = $evaluasi->pelatihan;

    $pdf = Pdf::loadView('pages.training.evaluasilevel3.peserta.pdf_view', [
        'pelatihan' => $pelatihan,
        'evaluasi' => $evaluasi,
        'jabatan_saat_pengajuan' => $jabatan_saat_pengajuan,
        'atasan_saat_pengajuan' => $atasan_saat_pengajuan,
    ]);

    return $pdf->download("Evaluasi_Level_3_{$pelatihan->kode_pelatihan}.pdf");
}
}
