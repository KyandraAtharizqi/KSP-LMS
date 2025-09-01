<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiLevel3PesertaActionPlan extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_3_peserta_action_plans';

    protected $fillable = [
        'pelatihan_id',
        'evaluasi_level_3_peserta_id',
        'user_id',
        'registration_id',
        'kode_pelatihan',
        'action_plan',
        'diaplikasikan',
        'frekuensi',
        'hasil',
    ];

    protected $casts = [
        'diaplikasikan' => 'boolean',
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
