<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengajuanPelatihanSignature extends Model
{
    use HasFactory;

    protected $table = 'surat_pengajuan_pelatihan_signatures';

    protected $fillable = [
        'surat_id',
        'user_id',
        'role', // mengusulkan / mengetahui / menyetujui
        'status', // approved / rejected / pending
        'signed_at',
        'note',
    ];

    protected $dates = ['signed_at'];

    public function surat()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'surat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
