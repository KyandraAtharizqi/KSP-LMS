<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelatihanPresenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'presenter_id',
        'type',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
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

