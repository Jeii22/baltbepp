<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait LogsAdminActivity
{
    /**
     * Log admin activity to database and log file
     */
    protected function logActivity(string $action, ?string $description = null, ?array $metadata = null): void
    {
        $user = auth()->user();
        
        if (!$user) {
            return;
        }
        
        // Log to database
        DB::table('admin_activity_logs')->insert([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => now(),
        ]);
        
        // Log to file for security monitoring
        Log::channel('security')->info("Admin Activity: {$action}", [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'description' => $description,
            'ip_address' => request()->ip(),
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * Log sensitive action (requires extra attention)
     */
    protected function logSensitiveAction(string $action, string $description, array $metadata = []): void
    {
        $this->logActivity($action, $description, array_merge($metadata, [
            'sensitive' => true,
            'requires_review' => true,
        ]));
        
        // Also log to a separate security channel
        Log::channel('security')->warning("SENSITIVE ACTION: {$action}", [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'description' => $description,
            'ip_address' => request()->ip(),
            'metadata' => $metadata,
        ]);
    }
}