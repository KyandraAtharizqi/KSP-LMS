<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiLevel1 extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_level_1s';

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'registration_id',
        'kode_pelatihan',
        'nama_pelatihan',
        'tanggal_pelaksanaan',
        'tempat',
        'name',
        'department',
        'jabatan_full',
        'superior_id',
        'ringkasan_isi_materi',
        'ide_saran_pengembangan',
        'komplain_saran_masukan',
        'is_submitted', // ✅ Add this
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'is_submitted' => 'boolean', // ✅ Cast it as boolean
    ];

    // Relationships

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function superior()
    {
        return $this->belongsTo(User::class, 'superior_id')->withDefault();
    }

    public function penyelenggaraan()
    {
        return $this->hasOne(EvaluasiLevel1Penyelenggaraan::class, 'evaluasi_level_1_id');
    }

    public function sarana()
    {
        return $this->hasOne(EvaluasiLevel1Sarana::class, 'evaluasi_level_1_id');
    }

    public function instrukturs()
    {
        return $this->hasMany(EvaluasiLevel1Instruktur::class, 'evaluasi_level_1_id');
    }

    public function materi()
    {
        return $this->hasOne(EvaluasiLevel1Materi::class, 'evaluasi_level_1_id');
    }
}
