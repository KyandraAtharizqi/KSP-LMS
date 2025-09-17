<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiLevel3Atasan extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_3_atasans';

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'atasan_id',
        'registration_id',
        'kode_pelatihan',
        'manfaat_pelatihan',
        'kinerja',
        'saran',
        'is_submitted',
    ];

    // Relations
    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user() // participant being evaluated
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atasan() // supervisor
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function tujuanPembelajarans()
    {
        return $this->hasMany(EvaluasiLevel3AtasanTujuanPembelajaran::class, 'evaluasi_level_3_atasan_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(EvaluasiLevel3AtasanFeedback::class, 'evaluasi_level_3_atasan_id');
    }

    public function participantSnapshot()
    {
        return $this->belongsTo(TrainingParticipant::class, 'user_id', 'user_id')
                    ->whereColumn('pelatihan_id', 'pelatihan_id');
    }
}
