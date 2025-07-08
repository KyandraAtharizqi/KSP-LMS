<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_id',
        'user_id',
        'registration_id',
        'jabatan_id',
        'department_id',
        'superior_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surat()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'surat_id');
    }
}
