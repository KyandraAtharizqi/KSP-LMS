<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'kode_pelatihan',
        'registration_id',

        // Snapshot fields
        'jabatan_id',
        'jabatan_full',
        'department_id',
        'directorate_id',
        'division_id',
        'superior_id',
        'golongan',
    ];

    /**
     * Related Surat Pengajuan Pelatihan (the training itself).
     */
    public function surat()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    /**
     * Related User (original person record).
     * NOTE: Only for reference — don't depend on this for historical data.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Related Jabatan from master data.
     * NOTE: Optional — may not exist if jabatan has been deleted.
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    /**
     * Related Department from master data.
     * NOTE: Optional — may not exist if department has been deleted.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Related Directorate from master data.
     */
    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    /**
     * Related Division from master data.
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * Related Superior (user) if still active.
     */
    public function superior()
    {
        return $this->belongsTo(User::class, 'superior_id');
    }
}
