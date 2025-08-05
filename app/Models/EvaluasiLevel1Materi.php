<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiLevel1Materi extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_1_materis';

    protected $fillable = [
        'evaluasi_level_1_id',
        'materi_sistematika',
        'materi_pemahaman',
        'materi_pengetahuan',
        'materi_manfaat',
        'materi_tujuan',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiLevel1::class, 'evaluasi_level_1_id');
    }
}
