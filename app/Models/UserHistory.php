<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserHistory extends Model
{
    use HasFactory;

    protected $table = 'user_histories';

    protected $fillable = [
        'user_id',
        'department_id',
        'division_id',
        'directorate_id',
        'jabatan_id',
        'superior_id',
        'effective_date',
        'recorded_at',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'recorded_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function superior()
    {
        return $this->belongsTo(User::class, 'superior_id');
    }
}
