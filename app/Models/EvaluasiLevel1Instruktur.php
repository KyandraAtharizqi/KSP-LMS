<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiLevel1Instruktur extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_1_instrukturs';

    protected $fillable = [
        'evaluasi_level_1_id',
        'user_id',       // for internal presenter
        'presenter_id',  // for external presenter
        'type',          // 'internal' or 'external'
        'instruktur_penguasaan',
        'instruktur_teknik',
        'instruktur_sistematika',
        'instruktur_waktu',
        'instruktur_proses',
    ];

    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiLevel1::class, 'evaluasi_level_1_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function presenter()
    {
        return $this->belongsTo(Presenter::class)->withDefault();
    }
}
