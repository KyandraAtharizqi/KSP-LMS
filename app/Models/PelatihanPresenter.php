<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelatihanPresenter extends Model
{
    use HasFactory;

     protected $fillable = [
        'pelatihan_id',
        'kode_pelatihan',
        'date',
        'type',
        'user_id',
        'presenter_id',

        // snapshot fields
        'user_name',
        'presenter_name',
        'presenter_institution',
        'jabatan_id',
        'jabatan_full',
        'department_id',
        'division_id',
        'directorate_id',
        'superior_id',
        'golongan',

        // attendance
        'check_in_time',
        'check_out_time',
        'submitted_by',
        'is_submitted',
        'submitted_at',
    ];

    protected $casts = [
        'date' => 'date',

        'submitted_at' => 'datetime',   // 👈 add this
    ];

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }


    public function getPresenterNameAttribute()
    {
        if ($this->type === 'internal' && $this->user) {
            return $this->user->name;
        } elseif ($this->type === 'external' && $this->presenter) {
            return $this->presenter->name;
        }
        return 'Unknown Presenter';
    }

}

