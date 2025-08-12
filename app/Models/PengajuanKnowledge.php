<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKnowledge extends Model
{
    protected $table = 'knowledge_pengajuan';
    
    protected $fillable = [
        'kode',
        'created_by',
        'kepada',
        'dari',
        'perihal',
        'judul',
        'pemateri',
        'tanggal',
        'peserta',
        'lampiran',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'peserta' => 'array'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
