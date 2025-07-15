<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengajuanPelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'kode_pelatihan',
        'kompetensi',
        'judul',
        'lokasi',
        'instruktur',
        'sifat',
        'kompetensi_wajib',
        'materi_global',
        'program_pelatihan_ksp',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'tempat',
        'penyelenggara',
        'biaya',
        'per_paket_or_orang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class, 'pelatihan_id');
    }

    public function approvals()
    {
        return $this->hasMany(SuratPengajuanPelatihanSignatureAndParaf::class, 'pelatihan_id');
    }

    public function signatures()
    {
        return $this->hasMany(SuratPengajuanPelatihanSignatureAndParaf::class, 'pelatihan_id');
    }

    // Dynamic approval tracking logic
    public function getApprovalStatus()
    {
        $approvals = $this->approvals;
        if ($approvals->isEmpty()) {
            return [
                'status' => 'not_started',
                'message' => 'No approval steps assigned yet.',
            ];
        }

        $currentRound = $approvals->max('round');
        $currentRoundSteps = $approvals->where('round', $currentRound)->sortBy('sequence');

        if ($currentRoundSteps->contains('status', 'rejected')) {
            $rejected = $currentRoundSteps->firstWhere('status', 'rejected');
            return [
                'status' => 'rejected',
                'current_round' => $currentRound,
                'current_sequence' => $rejected->sequence,
                'rejected_by' => optional($rejected->user)->name ?? '-',
                'reason' => $rejected->rejection_reason,
                'message' => "❌ Rejected by {$rejected->user->name} at step {$rejected->sequence}",
            ];
        }

        $next = $currentRoundSteps->firstWhere('status', 'pending');

        if ($next) {
            return [
                'status' => 'in_approval',
                'current_round' => $currentRound,
                'current_sequence' => $next->sequence,
                'next_approver' => optional($next->user)->name ?? '-',
                'message' => "⏳ Waiting for {$next->user->name} (Step {$next->sequence})",
            ];
        }

        return [
            'status' => 'approved',
            'current_round' => $currentRound,
            'message' => '✅ Fully approved',
        ];
    }
}
