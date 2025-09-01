<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiLevel3Peserta extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_3_pesertas';

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'registration_id',
        'kode_pelatihan',
        'manfaat_pelatihan',
        'kinerja',
        'saran',
        'is_submitted',
        'is_accepted',
    ];

    /**
     * Relations
     */

    // Evaluasi belongs to a Pelatihan (Surat Pengajuan Pelatihan)
    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    // Evaluasi belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // If you also track participant registration
    public function registration()
    {
        return $this->belongsTo(TrainingParticipant::class, 'registration_id');
    }

    public function feedbacks()
    {
    return $this->hasMany(EvaluasiLevel3PesertaFeedback::class, 'evaluasi_level_3_peserta_id');
    }

    public function actionPlans()
    {
    return $this->hasMany(EvaluasiLevel3PesertaActionPlan::class, 'evaluasi_level_3_peserta_id');
    }

    public function signatures()
    {
        return $this->hasMany(EvaluasiLevel3Signature::class, 'evaluasi_level_3_peserta_id');
    }
}
