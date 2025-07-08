<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'directorate_id'];

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function division()
    {
        return $this->belongsTo(Division::class);
    }

}
