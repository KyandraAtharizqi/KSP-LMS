<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelatihanLog extends Model
{
    protected $table = 'pelatihan_logs';

    protected $fillable = [
        'pelatihan_id',
        'kode_pelatihan',
        'user_id',
        'registration_id',
        'pengajuan_department_id',
        'current_department_id',
        'pengajuan_jabatan_full',
        'current_jabatan_full',
        'tanggal',
        'jam',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'decimal:2',
    ];

    // Relationships
    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function pengajuanDepartment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'pengajuan_department_id');
    }

    public function currentDepartment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'current_department_id');
    }
}
