<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvaluasiLevel3Peserta;
use App\Models\EvaluasiLevel3Atasan;
use App\Models\EvaluasiLevel3AtasanTujuanPembelajaran;
use App\Models\EvaluasiLevel3AtasanFeedback;
use App\Models\EvaluasiLevel3Signature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluasiLevel3AtasanController extends Controller
{
    // List evaluations for approval
    public function index()
    {
        $user = Auth::user();

        $evaluasis = in_array($user->role, ['admin', 'department_admin'])
            ? EvaluasiLevel3Peserta::with(['pelatihan', 'user', 'signatures' => fn($q) => $q->latest('round')])->get()
            : EvaluasiLevel3Peserta::whereHas('signatures', fn($q) => $q->where('approver_id', $user->id))
                ->with(['pelatihan', 'user', 'signatures' => fn($q) => $q->where('approver_id', $user->id)->latest('round')])
                ->get();

        return view('pages.training.evaluasilevel3.atasan.index', compact('evaluasis'));
    }

    // Show approval page
    public function approval($evaluasiId)
    {
        $user = Auth::user();

        $evaluasi = EvaluasiLevel3Peserta::with([
            'pelatihan',
            'user',
            'actionPlans',
            'feedbacks',
            'signatures' => fn($q) => $q->where('approver_id', $user->id)->latest('round')
        ])->findOrFail($evaluasiId);

        if (!$evaluasi->signatures->where('approver_id', $user->id)->first()) {
            abort(403, 'Anda tidak memiliki akses untuk approval ini.');
        }

        return view('pages.training.evaluasilevel3.atasan.approval', compact('evaluasi'));
    }

    // Submit approval (approve/reject)
    public function submitApproval(Request $request, $evaluasiId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $evaluasi = EvaluasiLevel3Peserta::findOrFail($evaluasiId);

        $signature = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasi->id)
            ->where('approver_id', $user->id)
            ->latest('round')
            ->firstOrFail();

        DB::transaction(function() use ($request, $evaluasi, $signature) {

            $signature->update([
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
                'signed_at' => now(),
            ]);

            if ($request->status === 'approved') {
                $evaluasi->update(['is_accepted' => 1]);

                // Create Atasan evaluation row if not exists
                EvaluasiLevel3Atasan::firstOrCreate(
                    [
                        'pelatihan_id' => $evaluasi->pelatihan_id,
                        'user_id' => $evaluasi->user_id,
                        'atasan_id' => $evaluasi->user->superior_id,
                    ],
                    [
                        'registration_id' => $evaluasi->registration_id,
                        'kode_pelatihan' => $evaluasi->kode_pelatihan,
                        'is_submitted' => false,
                    ]
                );
            } else {
                $evaluasi->update([
                    'is_accepted' => 0,
                    'is_submitted' => 0,
                ]);
            }
        });

        return redirect()->route('evaluasi-level-3.atasan.index')
            ->with('success', 'Approval berhasil disimpan.');
    }

    // Show Atasan evaluation form
public function create($evaluasiId)
{
    $evaluasiPeserta = EvaluasiLevel3Peserta::with([
        'pelatihan',
        'user',
        'user.jabatan',      // optional, to get jabatan info
        'user.department',   // optional, to get department info
        'atasanEvaluation'   // relationship to EvaluasiLevel3Atasan
    ])->findOrFail($evaluasiId);

    // Check if supervisor has approved participant evaluation
    $latestSignature = $evaluasiPeserta->signatures()
        ->where('approver_id', Auth::id())
        ->latest('round')
        ->first();

    if (!$latestSignature || $latestSignature->status !== 'approved') {
        abort(403, 'Anda harus menyetujui evaluasi peserta terlebih dahulu.');
    }

    // Fetch or create Atasan evaluation row
    $evaluasiAtasan = EvaluasiLevel3Atasan::firstOrCreate(
        [
            'pelatihan_id' => $evaluasiPeserta->pelatihan_id,
            'user_id' => $evaluasiPeserta->user_id,
            'atasan_id' => $evaluasiPeserta->user->superior_id,
        ],
        [
            'registration_id' => $evaluasiPeserta->registration_id,
            'kode_pelatihan' => $evaluasiPeserta->kode_pelatihan,
            'is_submitted' => false,
        ]
    );

    return view('pages.training.evaluasilevel3.atasan.form', compact(
        'evaluasiPeserta',
        'evaluasiAtasan'
    ));
}



    // Store Atasan evaluation
   // Store Atasan evaluation
    public function store(Request $request, $evaluasiId)
    {
        $request->validate([
            'tujuan.*' => 'required|string',
            'tercapai.*' => 'required|in:ya,tidak',
            'frekuensi.*' => 'required|integer|min:0|max:3',
            'hasil.*' => 'required|integer|min:1|max:4',
            'manfaat_pelatihan' => 'nullable|string',
            'kinerja' => 'nullable|integer|min:1|max:5',
            'saran' => 'nullable|string',
            'telah_mampu' => 'nullable|boolean',
            'tidak_diaplikasikan_karena' => 'nullable|string',
            'memberikan_informasi_mengenai' => 'nullable|string',
            'lain_lain' => 'nullable|string',
        ]);

        $evaluasi = EvaluasiLevel3Atasan::findOrFail($evaluasiId);

        DB::transaction(function () use ($request, $evaluasi) {

            // Update main evaluation row
            $evaluasi->update([
                'manfaat_pelatihan' => $request->manfaat_pelatihan,
                'kinerja' => $request->kinerja,
                'saran' => $request->saran,
                'is_submitted' => true,
            ]);

            // Save Tujuan Pembelajaran
            $tujuanData = $request->tujuan;
            $tercapaiData = $request->tercapai;
            $frekuensiData = $request->frekuensi;
            $hasilData = $request->hasil;

            foreach ($tujuanData as $index => $tujuan) {
                EvaluasiLevel3AtasanTujuanPembelajaran::updateOrCreate(
                    [
                        'evaluasi_level_3_atasan_id' => $evaluasi->id,
                        'tujuan_pembelajaran' => $tujuan,
                    ],
                    [
                        'pelatihan_id' => $evaluasi->pelatihan_id,
                        'user_id' => $evaluasi->user_id,
                        'atasan_id' => $evaluasi->atasan_id,
                        'registration_id' => $evaluasi->registration_id,
                        'kode_pelatihan' => $evaluasi->kode_pelatihan,
                        'diaplikasikan' => $tercapaiData[$index] === 'ya' ? 1 : 0,
                        'frekuensi' => $frekuensiData[$index],
                        'hasil' => $hasilData[$index],
                    ]
                );
            }

            // Save Feedback
            EvaluasiLevel3AtasanFeedback::updateOrCreate(
                ['evaluasi_level_3_atasan_id' => $evaluasi->id],
                [
                    'pelatihan_id' => $evaluasi->pelatihan_id,
                    'user_id' => $evaluasi->user_id,
                    'atasan_id' => $evaluasi->atasan_id,
                    'registration_id' => $evaluasi->registration_id,
                    'kode_pelatihan' => $evaluasi->kode_pelatihan,
                    'telah_mampu' => $request->has('telah_mampu') ? 1 : 0,
                    'tidak_diaplikasikan_karena' => $request->tidak_diaplikasikan_karena,
                    'memberikan_informasi_mengenai' => $request->memberikan_informasi_mengenai,
                    'lain_lain' => $request->lain_lain,
                ]
            );

        });

        return redirect()->route('evaluasi-level-3.atasan.index')
            ->with('success', 'Evaluasi Level 3 berhasil disimpan.');
    }

    public function preview($evaluasiId)
    {
        $evaluasiAtasan = EvaluasiLevel3Atasan::with([
            'pelatihan',
            'user',
            'atasan',
            'tujuanPembelajarans',
            'feedbacks'
        ])->findOrFail($evaluasiId);

        return view('pages.training.evaluasilevel3.atasan.preview', compact('evaluasiAtasan'));
    }

    public function downloadPdf($evaluasiId)
    {
        $evaluasiAtasan = EvaluasiLevel3Atasan::with([
            'pelatihan',
            'user',
            'user.jabatan',
            'user.department',
            'atasan',
            'atasan.jabatan',
            'tujuanPembelajarans',
            'feedbacks',
            'participantSnapshot',
            'participantSnapshot.department'
        ])->findOrFail($evaluasiId);

        $pdf = Pdf::loadView('pages.training.evaluasilevel3.atasan.pdf_view', compact('evaluasiAtasan'));
        
        $filename = 'Evaluasi_Level3_Atasan_' . 
                   str_replace(' ', '_', $evaluasiAtasan->user->name) . '_' .
                   $evaluasiAtasan->kode_pelatihan . '_' .
                   now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }



}
