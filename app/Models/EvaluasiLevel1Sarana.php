<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiLevel1Sarana extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_1_saranas';

    protected $fillable = [
        'evaluasi_level_1_id',
        'sarana_media',
        'sarana_kit',
        'sarana_kenyamanan',
        'sarana_kesesuaian',
        'sarana_belajar',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiLevel1::class, 'evaluasi_level_1_id');
    }
}
