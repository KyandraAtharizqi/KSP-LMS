<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'institution',
        'email',
        'phone',
        'notes',
    ];

    public function pelatihanPresenters()
    {
        return $this->hasMany(PelatihanPresenter::class, 'presenter_id');
    }
}
