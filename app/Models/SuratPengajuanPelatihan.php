<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengajuanPelatihan extends Model
{
    use HasFactory;

    protected $table = 'surat_pengajuan_pelatihan';

    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_pelatihan', // Custom surat number, like "SPP-2025-001"
        'created_by',
        'kompetensi',
        'judul',
        'lokasi',
        'instruktur',
        'sifat',
        'kompetensi_wajib',
        'materi_global',
        'program_pelatihan_ksp',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'tempat',
        'penyelenggara',
        'biaya',
        'per_paket_or_orang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signatures()
    {
        return $this->hasMany(SuratPengajuanPelatihanSignature::class, 'surat_id');
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class, 'surat_id');
    }
}
