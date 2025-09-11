<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratTugasPelatihan extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas_pelatihans';

    protected $fillable = [
        'pelatihan_id',
        'kode_pelatihan',
        'judul',
        'tanggal_mulai',
        'tempat',
        'tanggal_selesai',
        'durasi',
        'created_by',
        'status',
        'is_accepted',
        'tujuan',
        'waktu',
        'instruksi',
        'hal_perhatian',
        'catatan',
        'tanggal_pelaksanaan'
    ];
    
    protected $casts = [
        'tanggal_selesai' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_pelatihan' => 'array',
        'is_accepted' => 'boolean',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     | -----------------------------------------------------------------
     */

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All approvals (parafs and signatures), sorted by sequence.
     */
    public function signaturesAndParafs()
    {
        return $this->hasMany(SuratTugasPelatihanSignatureAndParaf::class, 'surat_tugas_id')
                    ->orderBy('sequence');
    }

    /**
     * Shortcut to only signatures
     */
    public function signatures()
    {
        return $this->signaturesAndParafs()->where('type', 'signature');
    }

    /**
     * Shortcut to only parafs
     */
    public function parafs()
    {
        return $this->signaturesAndParafs()->where('type', 'paraf');
    }

    /* -----------------------------------------------------------------
     | Approval Status Helper
     | -----------------------------------------------------------------
     */

    public function getApprovalStatus()
    {
        $approvals = $this->signaturesAndParafs;

        if ($approvals->isEmpty()) {
            return [
                'status' => 'not_started',
                'message' => 'Belum ada tahapan approval.',
            ];
        }

        if ($approvals->contains('status', 'rejected')) {
            $rejected = $approvals->firstWhere('status', 'rejected');
            return [
                'status' => 'rejected',
                'rejected_by' => optional($rejected->user)->name ?? '-',
                'reason' => $rejected->rejection_reason ?? 'Tidak diketahui',
                'message' => "❌ Ditolak oleh {$rejected->user->name}",
            ];
        }

        $next = $approvals->where('status', 'pending')->sortBy('sequence')->first();

        if ($next) {
            return [
                'status' => 'in_approval',
                'next_approver' => optional($next->user)->name ?? '-',
                'message' => "⏳ Menunggu persetujuan dari {$next->user->name}",
            ];
        }

        return [
            'status' => 'approved',
            'message' => '✅ Disetujui sepenuhnya',
        ];
    }

    /* -----------------------------------------------------------------
     | Model Events (Cascade Delete)
     | -----------------------------------------------------------------
     */
    protected static function booted()
    {
        static::deleting(function (self $model) {
            $model->signaturesAndParafs()->delete();
        });
    }


    public function approvals()
    {
        return $this->hasMany(SuratTugasPelatihanSignatureAndParaf::class, 'surat_tugas_id');
    }

}
