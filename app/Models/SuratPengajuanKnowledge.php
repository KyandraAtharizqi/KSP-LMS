<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratPengajuanKnowledge extends Model
{
    use HasFactory;

    protected $table = 'surat_pengajuan_knowledges';

    protected $fillable = [
        'tipe',
        'kode_pelatihan',
        'kode_knowledge',
        'judul',
        'materi',
        'pemateri',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'tanggal_pelaksanaan',
        'tempat',
        'penyelenggara',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'pemateri' => 'array', // JSON decode automatically
        'tanggal_pelaksanaan' => 'array', // JSON decode for multiple dates
    ];

    // Creator of this knowledge sharing
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // All parafs
    public function parafs()
    {
        return $this->hasMany(KnowledgeSignatureAndParaf::class, 'surat_pengajuan_knowledge_id')
                    ->where('type', 'paraf')
                    ->orderBy('sequence');
    }

    // All signatures
    public function signatures()
    {
        return $this->hasMany(KnowledgeSignatureAndParaf::class, 'surat_pengajuan_knowledge_id')
                    ->where('type', 'signature')
                    ->orderBy('sequence');
    }

    // Participants
    public function participants()
    {
        return $this->hasMany(KnowledgeParticipant::class, 'surat_pengajuan_knowledge_id');
    }
}
