<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiLevel3Signature extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_3_signatures';

    protected $fillable = [
        'evaluasi_level_3_peserta_id',
        'pelatihan_id',
        'approver_id',
        'registration_id',
        'kode_pelatihan',
        'round',
        'status',
        'rejection_reason',
        'signed_at',
        'jabatan_id',
        'jabatan_full',
        'department_id',
        'directorate_id',
        'division_id',
        'golongan',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiLevel3Peserta::class, 'evaluasi_level_3_peserta_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }
        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
