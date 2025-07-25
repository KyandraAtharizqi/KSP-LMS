<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class UserPositionHistory extends Model
{
    use HasFactory;

    protected $table = 'user_position_histories';

    protected $fillable = [
        'user_id',
        'registration_id',
        'directorate_id',
        'division_id',
        'department_id',
        'jabatan_id',
        'jabatan_full',
        'superior_id',
        'golongan',
        'is_active',
        'recorded_at',
        'effective_date',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'effective_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships (optional, depending on your needs)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
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
