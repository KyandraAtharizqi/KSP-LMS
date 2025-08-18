<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratUndangan extends Model
{
    protected $table = 'knowledge_pengajuan'; // Kita pakai tabel yang sama

    protected $fillable = [
        'kode',
        'created_by',
        'kepada',
        'dari',
        'perihal',
        'judul',
        'pemateri',
        'tanggal_mulai',
        'tanggal_selesai',
        'peserta',
        'lampiran',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'peserta' => 'array',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
