<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'kode_pelatihan',
        'registration_id',
        'jabatan_id',
        'department_id',
        'superior_id',
    ];

    public function surat()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
