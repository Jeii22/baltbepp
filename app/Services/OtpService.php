<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\TwoFactorCode;
use App\Models\User;

/**
 * Lightweight OTP service using cache for faster reads/writes.
 * Falls back to DB model generation for existing flows.
 */
class OtpService
{
    protected int $ttlSeconds;
    protected string $prefix = 'login_otp_';

    public function __construct(int $ttlSeconds = 600) // 10 minutes
    {
        $this->ttlSeconds = $ttlSeconds;
    }

    /**
     * Generate and store an OTP in cache; also persists a DB record for audit if desired.
     */
    public function generateForUser(User $user, string $type = 'login'): string
    {
        $otp = TwoFactorCode::generateCode();
        Cache::put($this->key($user->id, $type), bcrypt($otp), $this->ttlSeconds);
        // Optional: still create DB record for compatibility with existing verify flow
        TwoFactorCode::createForUser($user, $type);
        return $otp;
    }

    /**
     * Verify the OTP from cache first (fast). If fails, fall back to DB verification.
     */
    public function verifyForUser(User $user, string $otp, string $type = 'login'): bool
    {
        $cached = Cache::get($this->key($user->id, $type));
        if ($cached && password_verify($otp, $cached)) {
            Cache::forget($this->key($user->id, $type));
            return true;
        }
        return TwoFactorCode::verify($user, $otp, $type);
    }

    protected function key(int $userId, string $type): string
    {
        return $this->prefix.$type.'_'.$userId;
    }
}
