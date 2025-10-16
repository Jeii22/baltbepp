<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TwoFactorCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'type',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if code is valid
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark code as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Generate a new 6-digit code
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new code for a user
     */
    public static function createForUser(User $user, string $type = 'login'): self
    {
        // Invalidate old codes of the same type
        self::where('user_id', $user->id)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        return self::create([
            'user_id' => $user->id,
            'code' => self::generateCode(),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }

    /**
     * Verify a code for a user
     */
    public static function verify(User $user, string $code, string $type = 'login'): bool
    {
        $twoFactorCode = self::where('user_id', $user->id)
            ->where('code', $code)
            ->where('type', $type)
            ->where('used', false)
            ->first();

        if (!$twoFactorCode || !$twoFactorCode->isValid()) {
            return false;
        }

        $twoFactorCode->markAsUsed();
        return true;
    }
}
