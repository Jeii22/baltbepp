<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    // Helper accessors for common typed values
    public static function getBool(string $key, bool $default = false): bool
    {
        $val = static::query()->where('key', $key)->value('value');
        if ($val === null) return $default;
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public static function setBool(string $key, bool $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value ? '1' : '0']);
    }
}