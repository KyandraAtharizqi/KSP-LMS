<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeParticipant extends Model
{
    use HasFactory;

    protected $table = 'knowledge_participants';

    protected $fillable = [
        'surat_pengajuan_knowledge_id',
        'user_id',
        'jabatan_id',
        'jabatan_full',
        'division_id',
        'department_id',
        'superior_id',
        'golongan',
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
