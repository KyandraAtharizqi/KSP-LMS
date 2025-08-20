<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'value',
    ];

    public static function getValueByCode(string $code): string
    {
        $config = self::where('code', $code)->first();
        return $config ? $config->value : '';
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
