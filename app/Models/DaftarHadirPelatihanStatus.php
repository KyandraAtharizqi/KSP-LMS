<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use App\Models\SuratPengajuanPelatihan;
use App\Models\User;
use App\Models\DaftarHadirPelatihan;

class DaftarHadirPelatihanStatus extends Model
{
    use HasFactory;

    protected $table = 'daftar_hadir_pelatihan_status';

    protected $fillable = [
        'pelatihan_id',
        'date',
        'is_submitted',
        'submitted_at',
        'submitted_by',
        'presenter', 
    ];

    protected $casts = [
        'date'         => 'date',
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
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

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Attendance rows for this pelatihan *on this date*.
     * Because this uses an instance value ($this->date), eager loading works:
     * Eloquent will apply the whereDate constraint per parent row.
     */
    public function attendances()
    {
        return $this->hasMany(DaftarHadirPelatihan::class, 'pelatihan_id')
            ->whereDate('date', $this->date ?? now()->toDateString());
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isSubmitted(): bool
    {
        return (bool) $this->is_submitted;
    }

    public function submittedLabel(): string
    {
        return $this->isSubmitted() ? 'Submitted' : 'Pending';
    }

    public function formattedDate(string $format = 'd M Y'): string
    {
        return $this->date instanceof Carbon
            ? $this->date->format($format)
            : Carbon::parse($this->date)->format($format);
    }

    
        public function presenters()
    {
        return $this->hasMany(PelatihanPresenter::class, 'pelatihan_id', 'pelatihan_id');
    }
}
