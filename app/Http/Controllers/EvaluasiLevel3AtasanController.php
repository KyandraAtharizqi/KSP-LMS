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


class EvaluasiLevel3AtasanController extends Controller
{
    /**
     * Show list of evaluations for approval
     */
    public function index()
    {
        $user = Auth::user();

        // Supervisors see participant evaluations they are assigned to approve
        $evaluasis = EvaluasiLevel3Peserta::whereHas('signatures', function($q) use ($user) {
            $q->where('approver_id', $user->id);
        })->with(['pelatihan', 'user', 'signatures' => function($q) use ($user) {
            $q->where('approver_id', $user->id)->latest('round');
        }])->get();

        return view('pages.training.evaluasilevel3.atasan.index', compact('evaluasis'));
    }

    /**
     * Show approval page for a specific participant evaluation
     */
    public function approval($evaluasiId)
    {
        $user = Auth::user();

        $evaluasi = EvaluasiLevel3Peserta::with([
            'pelatihan',
            'user',
            'actionPlans',
            'feedbacks',
            'signatures' => function($q) use ($user) {
                $q->where('approver_id', $user->id)->latest('round');
            }
        ])->findOrFail($evaluasiId);

        // Only allow if this user is the assigned approver
        if (!$evaluasi->signatures->where('approver_id', $user->id)->first()) {
            abort(403, 'Anda tidak memiliki akses untuk approval ini.');
        }

        return view('pages.training.evaluasilevel3.atasan.approval', compact('evaluasi'));
    }

    /**
     * Handle submission of approval (approve or reject)
     */

    public function submitApproval(Request $request, $evaluasiId)
    {
        // Add logging to see if the method is being called
        \Log::info('submitApproval method called', [
            'evaluasiId' => $evaluasiId,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        try {
            $request->validate([
                'status' => 'required|in:approved,rejected',
                'rejection_reason' => 'nullable|string|max:255',
            ]);

            $user = Auth::user();
            $evaluasi = EvaluasiLevel3Peserta::findOrFail($evaluasiId);
            
            \Log::info('Found evaluation', ['evaluasi_id' => $evaluasi->id]);

            // Get the approver's signature row for this evaluation
            $signature = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasi->id)
                ->where('approver_id', $user->id)
                ->latest('round')
                ->first();

            if (!$signature) {
                \Log::error('Signature not found', [
                    'evaluasi_id' => $evaluasi->id,
                    'approver_id' => $user->id
                ]);
                abort(404, 'Signature record not found for this approver.');
            }

            \Log::info('Found signature', ['signature_id' => $signature->id]);

            // Update the signature record instead of creating a new one
            $signature->update([
                'status'           => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
                'signed_at'        => now(),
            ]);

            // Also update the peserta table (is_accepted)
            if ($request->status === 'approved') {
                $evaluasi->update(['is_accepted' => 1]);
            } else {
                $evaluasi->update(['is_accepted' => 0]);
            }

            \Log::info('Approval processed successfully', [
                'status' => $request->status,
                'evaluasi_id' => $evaluasi->id
            ]);

            return redirect()->route('evaluasi-level-3.atasan.index')
                ->with('success', 'Approval berhasil disimpan.');
                
        } catch (\Exception $e) {
            \Log::error('Error in submitApproval', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Show form for supervisor to fill their own evaluation after approval
     */
    public function create($evaluasiId)
    {
        $evaluasiPeserta = EvaluasiLevel3Peserta::findOrFail($evaluasiId);

        // Check if supervisor has approved participant evaluation
        $latestSignature = EvaluasiLevel3Signature::where('evaluasi_level_3_peserta_id', $evaluasiPeserta->id)
            ->where('approver_id', Auth::id())
            ->latest('round')
            ->first();

        if (!$latestSignature || $latestSignature->status !== 'approved') {
            abort(403, 'Anda harus menyetujui evaluasi peserta terlebih dahulu.');
        }

        return view('pages.training.evaluasilevel3.atasan.create', compact('evaluasiPeserta'));
    }

    public function store(Request $request, $evaluasiId)
        {
            $request->validate([
                'tujuan.*' => 'required|string',
                'tercapai.*' => 'required|in:ya,tidak',
                'catatan.*' => 'nullable|string',
                'manfaat_pelatihan' => 'nullable|string',
                'kinerja' => 'nullable|integer|min:1|max:5',
                'saran' => 'nullable|string',
            ]);

            $evaluasi = EvaluasiLevel3Atasan::findOrFail($evaluasiId);

            DB::transaction(function () use ($request, $evaluasi) {

                // Save general fields
                $evaluasi->update([
                    'manfaat_pelatihan' => $request->manfaat_pelatihan,
                    'kinerja' => $request->kinerja,
                    'saran' => $request->saran,
                    'is_submitted' => true,
                ]);

                // Save Tujuan Pembelajaran
                $tujuanData = $request->tujuan;
                $tercapaiData = $request->tercapai;
                $catatanData = $request->catatan;

                foreach ($tujuanData as $index => $tujuan) {
                    EvaluasiLevel3AtasanTujuanPembelajaran::create([
                        'pelatihan_id' => $evaluasi->pelatihan_id,
                        'evaluasi_level_3_atasan_id' => $evaluasi->id,
                        'user_id' => $evaluasi->user_id,
                        'atasan_id' => $evaluasi->atasan_id,
                        'registration_id' => $evaluasi->registration_id,
                        'kode_pelatihan' => $evaluasi->kode_pelatihan,
                        'tujuan_pembelajaran' => $tujuan,
                        'diaplikasikan' => $tercapaiData[$index] === 'ya' ? 1 : 0,
                        'frekuensi' => null, // optional if not used for Atasan
                        'hasil' => null,     // optional if not used for Atasan
                    ]);
                }

                // Save Feedback
                EvaluasiLevel3AtasanFeedback::create([
                    'pelatihan_id' => $evaluasi->pelatihan_id,
                    'evaluasi_level_3_atasan_id' => $evaluasi->id,
                    'user_id' => $evaluasi->user_id,
                    'atasan_id' => $evaluasi->atasan_id,
                    'registration_id' => $evaluasi->registration_id,
                    'kode_pelatihan' => $evaluasi->kode_pelatihan,
                    'telah_mampu' => $request->has('telah_mampu') ? 1 : 0,
                    'tidak_diaplikasikan_karena' => $request->tidak_diaplikasikan_karena,
                    'memberikan_informasi_mengenai' => $request->memberikan_informasi_mengenai,
                    'lain_lain' => $request->lain_lain,
                ]);

            });

            return redirect()->route('evaluasi-level-3.atasan.index')
                ->with('success', 'Evaluasi Level 3 berhasil disimpan.');
        }

}
