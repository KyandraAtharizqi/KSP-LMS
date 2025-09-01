<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiLevel3AtasanFeedback extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_3_atasans_feedbacks';

    protected $fillable = [
        'pelatihan_id',
        'evaluasi_level_3_atasan_id',
        'user_id',
        'atasan_id',
        'registration_id',
        'kode_pelatihan',
        'telah_mampu',
        'tidak_diaplikasikan_karena',
        'memberikan_informasi_mengenai',
        'lain_lain',
    ];

    // Relations
    public function evaluasiAtasan()
    {
        return $this->belongsTo(EvaluasiLevel3Atasan::class, 'evaluasi_level_3_atasan_id');
    }

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atasan()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }
}
