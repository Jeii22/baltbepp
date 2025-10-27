<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    protected $fillable = ['user_id', 'code', 'expires_at'];

    public static function createForUser($user, $context = 'default')
    {
        return self::create([
            'user_id' => $user->id,
            'code' => Str::random(6),
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}