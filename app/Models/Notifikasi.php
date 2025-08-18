<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    // Jika nama tabel di DB berbeda dari plural default
    protected $table = 'notifikasi';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'link',
        'dibaca'
    ];

    // Cast dibaca menjadi boolean otomatis
    protected $casts = [
        'dibaca' => 'boolean',
    ];

    // Relasi ke User (opsional tapi disarankan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
