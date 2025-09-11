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
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'peserta',
        'lampiran',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'peserta' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pesertaUsers()
    {
        $ids = collect($this->peserta)->pluck('id')->filter()->all();
        return \App\Models\User::whereIn('id', $ids)->get();
    }
}
