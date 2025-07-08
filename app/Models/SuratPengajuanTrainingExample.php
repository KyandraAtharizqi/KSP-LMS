<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPengajuanTrainingExample extends Model
{
    protected $table = 'surat_pengajuan_training_example';

    protected $fillable = [
        'title',
        'description',
        'training_date',
        'submitted_by',
        'status',
    ];

    protected $casts = [
        'training_date' => 'date',
    ];
}
