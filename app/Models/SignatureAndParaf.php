<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SignatureAndParaf extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'signature_path',
        'paraf_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'registration_id', 'registration_id');
    }

    
}