<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugasPelatihanSignatureAndParaf extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas_pelatihan_signatures_and_parafs';

    protected $fillable = [
        'surat_tugas_id',
        'user_id',
        'type',              // 'paraf' or 'signature'
        'round',
        'sequence',
        'status',            // 'pending', 'approved', 'rejected'
        'signed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    // Relationships
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugasPelatihan::class, 'surat_tugas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Type helpers
    public function isParaf()
    {
        return $this->type === 'paraf';
    }

    public function isSignature()
    {
        return $this->type === 'signature';
    }

    // Status helpers
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPending()
    {
        return $this->status === null || $this->status === 'pending';
    }
}
