<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiLevel3PesertaFeedback extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_3_peserta_feedbacks';

    protected $fillable = [
        'pelatihan_id',
        'evaluasi_level_3_peserta_id',
        'user_id',
        'registration_id',
        'kode_pelatihan',
        'telah_mampu',
        'membantu_mengaplikasikan',
        'tidak_diaplikasikan_karena',
        'memberikan_informasi_mengenai',
        'lain_lain',
    ];

    protected $casts = [
        'telah_mampu' => 'boolean',
        'membantu_mengaplikasikan' => 'boolean',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiLevel3Peserta::class, 'evaluasi_level_3_peserta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }
}
