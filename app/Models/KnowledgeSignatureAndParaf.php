<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeSignatureAndParaf extends Model
{
    use HasFactory;

    protected $table = 'knowledge_signature_and_parafs';

    protected $fillable = [
        'surat_pengajuan_knowledge_id',
        'kode_pelatihan',
        'user_id',
        'registration_id',
        'type',
        'sequence',
        'round',
        'status',
        'rejection_reason',
        'signed_at',
        'jabatan_id',
        'jabatan_full',
        'department_id',
        'directorate_id',
        'division_id',
        'superior_id',
        'golongan',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    // Link to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Link to knowledge sharing
    public function surat()
    {
        return $this->belongsTo(SuratPengajuanKnowledge::class, 'surat_pengajuan_knowledge_id');
    }
}
