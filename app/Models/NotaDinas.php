<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaDinas extends Model
{
    use HasFactory;

    protected $table = 'nota_dinas';

    protected $fillable = [
        'kode',
        'perihal',
        'tanggal',
        'dari',
        'kepada',
        'judul',
        'pemateri',
        'lampiran',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
