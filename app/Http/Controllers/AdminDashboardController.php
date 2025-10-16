<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with security checks
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Log admin dashboard access
        $this->logAdminAccess($request, $user);
        
        // Check if admin has 2FA enabled (warning if not)
        $twoFactorWarning = !$user->two_factor_enabled;
        
        // Check for suspicious activity
        $securityAlerts = $this->getSecurityAlerts($user);
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent activity
        $recentBookings = Booking::with('trip')
            ->latest()
            ->limit(5)
            ->get();
        
        // Check for locked accounts (security concern)
        $lockedAccountsCount = User::whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->count();
        
        // Get failed login attempts in last hour
        $recentFailedLogins = DB::table('login_attempts')
            ->where('successful', false)
            ->where('attempted_at', '>', now()->subHour())
            ->count();
        
        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'twoFactorWarning',
            'securityAlerts',
            'lockedAccountsCount',
            'recentFailedLogins'
        ));
    }
    
    /**
     * Log admin access for security audit
     */
    private function logAdminAccess(Request $request, User $user)
    {
        Log::channel('security')->info('Admin Dashboard Access', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);
        
        // Store in database for audit trail
        DB::table('admin_activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'dashboard_access',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }
    
    /**
     * Get security alerts for the admin
     */
    private function getSecurityAlerts(User $user): array
    {
        $alerts = [];
        
        // Check if 2FA is disabled
        if (!$user->two_factor_enabled) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Two-Factor Authentication is disabled. Enable it for better security.',
                'action_url' => route('profile.edit'),
                'action_text' => 'Enable 2FA',
            ];
        }
        
        // Check if password is old (not changed in 90 days)
        if ($user->password_changed_at && $user->password_changed_at->lt(now()->subDays(90))) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Your password is over 90 days old. Consider changing it.',
                'action_url' => route('profile.edit'),
                'action_text' => 'Change Password',
            ];
        }
        
        // Check for unusual login location
        if ($user->last_login_ip && $user->last_login_ip !== request()->ip()) {
            $alerts[] = [
                'type' => 'info',
                'message' => 'Login from new IP address detected: ' . request()->ip(),
                'action_url' => null,
                'action_text' => null,
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        return [
            'today_bookings' => Booking::whereDate('created_at', today())->count(),
            'today_passengers' => Booking::whereDate('created_at', today())
                ->sum(DB::raw('adult + child + infant + pwd')),
            'today_revenue' => Booking::whereDate('created_at', today())
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'total_users' => User::count(),
            'active_trips' => Trip::where('departure_time', '>=', now())->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
        ];
    }
    
    /**
     * Get security overview data
     */
    public function securityOverview()
    {
        $user = auth()->user();
        
        // Only super admins can view security overview
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to security overview.');
        }
        
        $data = [
            'locked_accounts' => User::whereNotNull('locked_until')
                ->where('locked_until', '>', now())
                ->get(['id', 'name', 'email', 'locked_until', 'failed_login_attempts']),
            
            'recent_failed_logins' => DB::table('login_attempts')
                ->where('successful', false)
                ->where('attempted_at', '>', now()->subDay())
                ->orderBy('attempted_at', 'desc')
                ->limit(20)
                ->get(),
            
            'users_without_2fa' => User::where('two_factor_enabled', false)
                ->whereIn('role', ['admin', 'super_admin'])
                ->get(['id', 'name', 'email', 'role']),
            
            'recent_admin_activity' => DB::table('admin_activity_logs')
                ->join('users', 'admin_activity_logs.user_id', '=', 'users.id')
                ->select('admin_activity_logs.*', 'users.name', 'users.email')
                ->orderBy('admin_activity_logs.created_at', 'desc')
                ->limit(50)
                ->get(),
        ];
        
        return view('admin.security-overview', $data);
    }
}