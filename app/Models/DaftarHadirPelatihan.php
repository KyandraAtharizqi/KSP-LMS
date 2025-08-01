<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class DaftarHadirPelatihan extends Model
{
    use HasFactory;

    protected $table = 'daftar_hadir_pelatihan';

    protected $fillable = [
        'pelatihan_id',
        'user_id',
        'registration_id',
        'date',
        'check_in_time',
        'check_in_timestamp',
        'check_in_photo',
        'check_out_time',
        'check_out_timestamp',
        'check_out_photo',
        'status',  // hadir, sakit, izin, absen
        'note',
    ];

    protected $casts = [
        'date'                => 'date',
        'check_in_timestamp'  => 'datetime',
        'check_out_timestamp' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'absen', // Default status
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function pelatihan()
    {
        return $this->belongsTo(SuratPengajuanPelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Derived Duration Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Duration (minutes) based on input times (not timestamps) if both exist.
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }
        try {
            $in  = Carbon::createFromFormat('H:i:s', $this->normalizeTimeString($this->check_in_time));
            $out = Carbon::createFromFormat('H:i:s', $this->normalizeTimeString($this->check_out_time));
            return max(0, $in->diffInMinutes($out));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Duration (hours, decimal) convenience.
     */
    public function getDurationHoursAttribute(): ?float
    {
        $min = $this->duration_minutes;
        return is_null($min) ? null : round($min / 60, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForDay($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForPelatihan($query, $pelatihanId)
    {
        return $query->where('pelatihan_id', $pelatihanId);
    }

    /*
    |--------------------------------------------------------------------------
    | Internal helpers
    |--------------------------------------------------------------------------
    */

    protected function normalizeTimeString(string $val): string
    {
        // Accepts "08:05", "8:5", "08:05:00"
        try {
            return Carbon::parse($val)->format('H:i:s');
        } catch (\Throwable $e) {
            return '00:00:00';
        }
    }


}
