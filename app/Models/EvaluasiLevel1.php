<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiLevel1 extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_1';

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'materi',
        'narasumber',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
