<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPengajuanPelatihanSignatureAndParaf extends Model
{
    protected $table = 'surat_pengajuan_pelatihan_signatures_and_parafs';

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'kode_pelatihan',
        'registration_id',
        'round',
        'sequence',
        'type', // 'paraf' or 'signature'
        'status', // 'pending', 'approved', 'rejected'
        'signed_at',
        'rejection_reason',
    ];

    /**
     * Get the user (paraf/signature approver).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related Surat Pengajuan Pelatihan.
     */
    public function surat(): BelongsTo
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }
}
