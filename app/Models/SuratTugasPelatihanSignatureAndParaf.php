<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugasPelatihanSignatureAndParaf extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas_pelatihan_signatures_and_parafs';

    /**
     * Mass assignable.
     * NOTE: kode_pelatihan & registration_id are denormalized snapshot values.
     */
    protected $fillable = [
        'surat_tugas_id',
        'kode_pelatihan',     // snapshot (string, NOT FK)
        'user_id',
        'registration_id',    // snapshot (string, NOT FK)
        'jabatan_id',
        'jabatan_full',
        'department_id',
        'directorate_id',
        'division_id',
        'superior_id',
        'golongan',
        'type',               // 'paraf' or 'signature'
        'round',
        'sequence',
        'status',             // 'pending', 'approved', 'rejected'
        'signed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => 'pending',
        'round' => 1,
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugasPelatihan::class, 'surat_tugas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* -----------------------------------------------------------------
     |  Model Events - auto fill snapshot fields
     | -----------------------------------------------------------------
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // kode_pelatihan snapshot (from related Surat Tugas)
            if (empty($model->kode_pelatihan) && $model->surat_tugas_id) {
                if ($suratTugas = SuratTugasPelatihan::find($model->surat_tugas_id)) {
                    $model->kode_pelatihan = $suratTugas->kode_pelatihan;
                }
            }

            // registration_id snapshot (from related User)
            if (empty($model->registration_id) && $model->user_id) {
                if ($user = User::find($model->user_id)) {
                    // adjust if your users table column name differs
                    $model->registration_id = $user->registration_id ?? null;
                }
            }
        });
    }

    /* -----------------------------------------------------------------
     |  Type Helpers
     | -----------------------------------------------------------------
     */
    public function isParaf(): bool
    {
        return $this->type === 'paraf';
    }

    public function isSignature(): bool
    {
        return $this->type === 'signature';
    }

    /* -----------------------------------------------------------------
     |  Status Helpers
     | -----------------------------------------------------------------
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' || $this->status === null;
    }

    /* -----------------------------------------------------------------
     |  Query Scopes (optional convenience)
     | -----------------------------------------------------------------
     */
    public function scopeParaf($query)
    {
        return $query->where('type', 'paraf');
    }

    public function scopeSignature($query)
    {
        return $query->where('type', 'signature');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
