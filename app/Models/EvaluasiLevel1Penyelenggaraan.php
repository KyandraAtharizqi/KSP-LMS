<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiLevel1Penyelenggaraan extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_1_penyelenggaraans';

    protected $fillable = [
        'evaluasi_level_1_id',
        'penyelenggaraan_pengelolaan',
        'penyelenggaraan_jadwal',
        'penyelenggaraan_persiapan',
        'penyelenggaraan_pelayanan',
        'penyelenggaraan_koordinasi',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiLevel1::class, 'evaluasi_level_1_id');
    }
}
