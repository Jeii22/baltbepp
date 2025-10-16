# Security Testing Guide

## 🧪 Complete Security Feature Testing

This guide will help you test all the security features that have been implemented in the Balt-Bep Ferries application.

---

## Prerequisites

1. **Database migrations run**: ✅ Completed
2. **Server running**: `php artisan serve`
3. **Test accounts**:
   - Super Admin account (existing)
   - Regular user account (for testing)

---

## 1. Admin Activity Logging

### Test Dashboard Access Logging
1. Login as admin/super_admin
2. Navigate to `/admin/dashboard`
3. Check database:
   ```sql
   SELECT * FROM admin_activity_logs ORDER BY created_at DESC LIMIT 5;
   ```
4. **Expected**: Entry with action `dashboard_accessed`

### Test User Management Logging
1. Go to Users page
2. Create a new user
3. Check logs:
   ```sql
   SELECT * FROM admin_activity_logs WHERE action = 'user_created';
   ```
4. **Expected**: Entry with user details in metadata

5. Delete a user
6. Check logs:
   ```sql
   SELECT * FROM admin_activity_logs WHERE action = 'user_deleted';
   ```
7. **Expected**: Entry with deleted user info

### Test Booking Management Logging
1. Go to Bookings page
2. Update a booking status
3. Check logs:
   ```sql
   SELECT * FROM admin_activity_logs WHERE action = 'booking_status_updated';
   ```
4. **Expected**: Entry with old/new status

### Test Trip Management Logging
1. Go to Trips page
2. Create a new trip
3. Check logs:
   ```sql
   SELECT * FROM admin_activity_logs WHERE action = 'trip_created';
   ```
4. **Expected**: Entry with trip details

5. Update a trip
6. Check logs:
   ```sql
   SELECT * FROM admin_activity_logs WHERE action = 'trip_updated';
   ```
7. **Expected**: Entry with old and new data

8. Delete a trip
9. Check logs:
   ```sql
   SELECT * FROM admin_activity_logs WHERE action = 'trip_deleted';
   ```
10. **Expected**: Entry with deleted trip info

---

## 2. Security Alerts System

### Test 2FA Disabled Alert
1. Login as admin with 2FA disabled
2. Go to dashboard
3. **Expected**: Yellow alert box at top saying "Two-Factor Authentication is disabled"
4. Click "Enable it now" link
5. **Expected**: Redirected to profile page

### Test Password Age Alert
1. Update your password_changed_at to 100 days ago:
   ```sql
   UPDATE users SET password_changed_at = DATE_SUB(NOW(), INTERVAL 100 DAY) WHERE id = YOUR_ID;
   ```
2. Refresh dashboard
3. **Expected**: Yellow alert saying "Your password is over 90 days old"

### Test New IP Address Alert
1. Login from different IP (or simulate by updating last_login_ip)
2. Go to dashboard
3. **Expected**: Blue info alert about new IP address

---

## 3. Security Metrics Dashboard

### Test Super Admin Security Overview
1. Login as super_admin
2. Go to dashboard
3. **Expected**: "Security Overview" section visible
4. Check three metric cards:
   - **Locked Accounts**: Shows count of locked users
   - **Failed Logins (1h)**: Shows recent failed attempts
   - **2FA Enabled**: Shows count of users with 2FA

### Test Security Overview Page
1. Click "View Details" in Security Overview section
2. **Expected**: Redirected to `/admin/security/overview`
3. Verify sections:
   - **Locked Accounts**: Table with unlock buttons
   - **Users Without 2FA**: List of admin/super_admin without 2FA
   - **Recent Failed Logins**: Last 24 hours
   - **Recent Admin Activity**: Latest admin actions

### Test Account Unlock Feature
1. Lock a test account:
   ```sql
   UPDATE users SET locked_until = DATE_ADD(NOW(), INTERVAL 30 MINUTE), failed_login_attempts = 3 WHERE id = TEST_USER_ID;
   ```
2. Go to Security Overview page
3. Find locked account in table
4. Click "Unlock" button
5. **Expected**: Account unlocked, success message shown
6. Verify in database:
   ```sql
   SELECT locked_until, failed_login_attempts FROM users WHERE id = TEST_USER_ID;
   ```
7. **Expected**: Both fields should be NULL/0

---

## 4. Session Timeout Warning

### Test Session Warning
1. Login as admin
2. Wait for (session_lifetime - 5) minutes
   - Default: 120 minutes, so wait 115 minutes
   - For testing, temporarily change `SESSION_LIFETIME=10` in .env (10 minutes)
3. **Expected**: Browser alert appears saying "Your session will expire in 5 minutes"
4. Click "OK"
5. **Expected**: Session extended, no logout

### Test Session Expiry
1. Set `SESSION_LIFETIME=5` in .env for testing
2. Login as admin
3. Wait 5 minutes without activity
4. Try to navigate to any admin page
5. **Expected**: Redirected to login page

---

## 5. Password Age Tracking

### Test Password Change Tracking
1. Go to Profile page
2. Change your password
3. Check database:
   ```sql
   SELECT password_changed_at FROM users WHERE id = YOUR_ID;
   ```
4. **Expected**: Timestamp updated to current time

### Test Password Age Warning
1. Set password_changed_at to 91 days ago:
   ```sql
   UPDATE users SET password_changed_at = DATE_SUB(NOW(), INTERVAL 91 DAY) WHERE id = YOUR_ID;
   ```
2. Go to dashboard
3. **Expected**: Yellow warning alert about password age

---

## 6. Enhanced Middleware

### Test RequireAdminTwoFactor Middleware (Optional)
1. Apply middleware to a route in `routes/web.php`:
   ```php
   Route::get('/admin/sensitive', function() {
       return 'Sensitive page';
   })->middleware(['auth', 'requireAdmin2FA']);
   ```
2. Login as admin WITHOUT 2FA enabled
3. Try to access `/admin/sensitive`
4. **Expected**: Redirected to profile with error message
5. Enable 2FA
6. Try again
7. **Expected**: Access granted

### Test RequirePasswordConfirmation Middleware (Optional)
1. Apply middleware to a route:
   ```php
   Route::delete('/users/{user}', [UserController::class, 'destroy'])
       ->middleware(['auth', 'password.confirm']);
   ```
2. Try to delete a user
3. **Expected**: Redirected to password confirmation page
4. Enter password
5. **Expected**: Action completed

---

## 7. Security Log File

### Test Security Log Channel
1. Perform any admin action (create user, update booking, etc.)
2. Check log file: `storage/logs/security.log`
3. **Expected**: Log entries with:
   - Timestamp
   - Admin user info
   - Action performed
   - IP address
   - User agent
   - Metadata

### Sample Log Entry
```
[2025-01-15 10:30:45] security.INFO: Admin Activity: user_created {"user_id":5,"admin_id":1,"admin_name":"Super Admin","action":"user_created","description":"Created user: John Doe (john@example.com) with role admin","ip_address":"127.0.0.1","user_agent":"Mozilla/5.0...","metadata":{"user_id":5,"email":"john@example.com","role":"admin"}}
```

---

## 8. Integration Testing

### Test Complete Admin Workflow
1. Login as super_admin
2. Check dashboard for security alerts
3. Review security metrics
4. Go to Security Overview page
5. Check recent admin activity
6. Unlock a locked account (if any)
7. Create a new user
8. Update a booking status
9. Create a new trip
10. Check security logs
11. Verify all actions logged

### Test Security Monitoring
1. Create multiple failed login attempts:
   - Try to login with wrong password 3 times
2. Check Security Overview page
3. **Expected**: Failed login attempts visible
4. Check if account is locked
5. Unlock account as super_admin
6. Verify unlock is logged

---

## 9. Database Verification

### Check All Security Tables
```sql
-- Admin activity logs
SELECT COUNT(*) as total_logs FROM admin_activity_logs;
SELECT action, COUNT(*) as count FROM admin_activity_logs GROUP BY action;

-- Password tracking
SELECT email, password_changed_at, 
       DATEDIFF(NOW(), password_changed_at) as days_old 
FROM users 
WHERE password_changed_at IS NOT NULL;

-- Locked accounts
SELECT email, locked_until, failed_login_attempts 
FROM users 
WHERE locked_until > NOW() OR failed_login_attempts > 0;

-- 2FA adoption
SELECT 
    COUNT(*) as total_users,
    SUM(two_factor_enabled) as with_2fa,
    ROUND(SUM(two_factor_enabled)/COUNT(*)*100, 2) as adoption_rate
FROM users
WHERE role IN ('admin', 'super_admin');
```

---

## 10. Performance Testing

### Test Log Query Performance
```sql
-- Should be fast due to indexes
EXPLAIN SELECT * FROM admin_activity_logs WHERE user_id = 1 ORDER BY created_at DESC LIMIT 20;
EXPLAIN SELECT * FROM admin_activity_logs WHERE action = 'user_created' ORDER BY created_at DESC LIMIT 20;
EXPLAIN SELECT * FROM admin_activity_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

**Expected**: All queries should use indexes (type = ref or range)

---

## 11. Security Best Practices Verification

### Checklist
- [ ] All admin actions are logged
- [ ] Security alerts appear on dashboard
- [ ] Super admin can view security metrics
- [ ] Account unlock works correctly
- [ ] Session timeout warning appears
- [ ] Password age is tracked
- [ ] Security logs are written to file
- [ ] Database indexes are working
- [ ] Middleware is registered
- [ ] No sensitive data in logs (passwords, tokens)

---

## 12. Common Issues & Solutions

### Issue: Security alerts not showing
**Solution**: Check if controller is passing variables to view:
```php
// In AdminDashboardController
return view('admin.dashboard', [
    'securityAlerts' => $this->getSecurityAlerts(),
    'lockedAccountsCount' => $lockedAccountsCount,
    'recentFailedLogins' => $recentFailedLogins,
]);
```

### Issue: Activity not being logged
**Solution**: Verify trait is imported and used:
```php
use App\Traits\LogsAdminActivity;

class YourController extends Controller
{
    use LogsAdminActivity;
    
    // Then call: $this->logActivity('action', 'description', $metadata);
}
```

### Issue: Security log file not created
**Solution**: Check permissions and create manually:
```bash
New-Item -ItemType File -Path "storage\logs\security.log" -Force
```

### Issue: Session timeout not working
**Solution**: Check .env configuration:
```env
SESSION_LIFETIME=120
SESSION_DRIVER=database
```

---

## 13. Production Deployment Checklist

Before deploying to production:

- [ ] Run all migrations: `php artisan migrate`
- [ ] Clear all caches: `php artisan optimize:clear`
- [ ] Set proper session lifetime in .env
- [ ] Configure log rotation for security.log
- [ ] Test all security features in staging
- [ ] Review and adjust password age threshold (currently 90 days)
- [ ] Consider applying 2FA middleware to all admin routes
- [ ] Set up automated log monitoring/alerts
- [ ] Document security procedures for team
- [ ] Train admins on new security features

---

## 14. Monitoring & Maintenance

### Daily Tasks
- Review security.log for suspicious activity
- Check locked accounts in Security Overview
- Monitor failed login attempts

### Weekly Tasks
- Review admin activity logs
- Check 2FA adoption rate
- Verify all security features working

### Monthly Tasks
- Archive old security logs
- Review and update security policies
- Test account unlock procedures
- Verify password age warnings

---

## 🎉 Testing Complete!

Once all tests pass, your security implementation is ready for production use.

**Security Level**: 🔒🔒🔒🔒🔒 (5/5 - Enterprise Grade)

For questions or issues, refer to:
- `SECURITY_IMPLEMENTATION_SUMMARY.md` - Complete feature list
- `SECURITY_FEATURES.md` - Detailed documentation
- `SECURITY_SETUP.md` - Setup instructions