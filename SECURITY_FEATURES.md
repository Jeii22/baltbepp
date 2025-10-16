# Security Features Implementation Guide

## Overview
This document outlines all the security enhancements implemented in the Balt-Bep Ferries booking system.

---

## 🔐 Password Security

### Strong Password Policy
All passwords must meet the following requirements:
- **Minimum 8 characters**
- **At least 1 uppercase letter** (A-Z)
- **At least 1 lowercase letter** (a-z)
- **At least 1 number** (0-9)
- **At least 1 special character** (!@#$%^&*()_+-=[]{}|;:,.<>?)

### Password Hashing
- **Primary Algorithm**: Argon2id (most secure, recommended)
- **Fallback**: Bcrypt with 12 rounds
- Configured in `.env`:
  ```
  HASH_DRIVER=argon2id
  BCRYPT_ROUNDS=12
  ```

### How It Works
- Passwords are hashed using Argon2id by default
- If Argon2id is not available, falls back to Bcrypt
- Passwords are never stored in plain text
- Each password has a unique salt

---

## 🔒 Multi-Factor Authentication (2FA)

### Email-Based OTP
- **6-digit verification codes** sent to Gmail
- **10-minute expiration** for security
- **One-time use** - codes cannot be reused

### When 2FA is Triggered
1. **Registration**: New users receive OTP to verify email
2. **Login**: Users with 2FA enabled receive OTP
3. **Password Reset**: OTP required to reset password

### User Management
Users can enable/disable 2FA from their profile:
- Navigate to **Profile → Security Settings**
- Click **Enable** to activate 2FA
- Verify with OTP sent to email
- Disable requires password confirmation

### Implementation Details
- Codes stored in `two_factor_codes` table
- Automatic cleanup of expired/used codes
- Rate limiting on code generation
- Resend functionality with cooldown

---

## 🚫 Failed Login Protection

### Account Lockout System
- **3 failed attempts** triggers account lock
- **30-minute lockout period**
- Automatic unlock after timeout
- Manual unlock by administrators

### Rate Limiting
- **3 login attempts** per email/IP combination
- Throttle window resets on successful login
- Clear error messages for locked accounts

### Tracking
All login attempts are logged:
- Email address
- IP address
- User agent
- Timestamp
- Success/failure status

---

## 🍪 Session Management

### Secure Cookie Configuration
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120          # 2 hours
SESSION_ENCRYPT=true          # Encrypted sessions
SESSION_HTTP_ONLY=true        # Prevent JavaScript access
SESSION_SAME_SITE=lax         # CSRF protection
SESSION_SECURE_COOKIE=false   # Set to true in production with HTTPS
```

### Session Security Features
1. **Database Storage**: Sessions stored in database, not files
2. **Encryption**: All session data encrypted
3. **HTTP Only**: Cookies inaccessible to JavaScript
4. **SameSite**: Protection against CSRF attacks
5. **Automatic Expiration**: 2-hour idle timeout
6. **Regeneration**: Session ID regenerated on login

### Session Termination
- **On Logout**: Complete session invalidation
- **On Timeout**: Automatic cleanup after 2 hours
- **On Lock**: Immediate termination if account locked

---

## 👥 Role-Based Access Control (RBAC)

### User Roles
1. **Super Admin**: Full system access
2. **Admin**: Administrative functions
3. **User/Customer**: Booking and profile management

### Middleware Protection
```php
// Protect routes by role
Route::middleware(['auth', 'role:super_admin,admin'])->group(function () {
    // Admin-only routes
});
```

### Available Middleware
- `role:super_admin` - Super admin only
- `role:admin` - Admin only
- `role:user` - Regular users only
- `role:super_admin,admin` - Multiple roles
- `checkLocked` - Verify account not locked

### Access Control Features
- Automatic role-based redirects after login
- 403 Forbidden for unauthorized access
- Role verification on every request
- Sensitive data filtered by role

---

## 📧 Gmail-Only Registration

### Email Validation
- Only `@gmail.com` addresses accepted
- Regex validation: `/^[a-zA-Z0-9._%+-]+@gmail\.com$/`
- Clear error message for non-Gmail addresses

### Why Gmail Only?
- Reliable email delivery
- Better spam filtering
- Consistent OTP delivery
- Reduced bounce rates

---

## 🔧 Configuration

### Environment Variables
Update your `.env` file:

```env
# Hashing
HASH_DRIVER=argon2id
BCRYPT_ROUNDS=12

# Session Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_SECURE_COOKIE=false  # Set to true in production

# Email Configuration (for OTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@baltbep.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Gmail App Password Setup
1. Go to Google Account settings
2. Enable 2-Step Verification
3. Generate App Password:
   - Go to Security → App passwords
   - Select "Mail" and "Windows Computer"
   - Copy the 16-character password
4. Use this password in `MAIL_PASSWORD`

---

## 🛡️ Security Best Practices

### For Production
1. **Enable HTTPS**:
   ```env
   SESSION_SECURE_COOKIE=true
   ```

2. **Update Session Settings**:
   ```env
   SESSION_SAME_SITE=strict
   ```

3. **Configure CORS** properly

4. **Enable Rate Limiting** on all public endpoints

5. **Regular Security Audits**

### For Administrators
1. **Monitor Failed Login Attempts**:
   - Check `login_attempts` table regularly
   - Investigate suspicious patterns

2. **Review Locked Accounts**:
   - Verify legitimate lockouts
   - Manually unlock if needed

3. **Audit User Roles**:
   - Ensure proper role assignments
   - Remove unnecessary admin privileges

4. **Check 2FA Adoption**:
   - Encourage users to enable 2FA
   - Consider making it mandatory for admins

---

## 📊 Database Tables

### `users` Table (New Fields)
- `two_factor_enabled` - Boolean flag
- `two_factor_secret` - Encrypted secret (future use)
- `failed_login_attempts` - Counter
- `locked_until` - Timestamp for lockout
- `last_login_at` - Last successful login
- `last_login_ip` - IP address of last login

### `two_factor_codes` Table
- `user_id` - Foreign key to users
- `code` - 6-digit OTP
- `type` - login, registration, password_reset
- `expires_at` - Expiration timestamp
- `used` - Boolean flag

### `login_attempts` Table (Existing)
- Tracks all login attempts
- Used for security monitoring

---

## 🔍 Testing

### Test Password Policy
```php
// Valid passwords
"Password123!"
"MyP@ssw0rd"
"Secure#2024"

// Invalid passwords
"password"        // No uppercase, number, special char
"PASSWORD123"     // No lowercase, special char
"Password123"     // No special char
"Pass1!"          // Too short
```

### Test Account Lockout
1. Attempt login with wrong password 3 times
2. Account should lock for 30 minutes
3. Verify error message displays remaining time
4. Wait or manually unlock via database

### Test 2FA Flow
1. Enable 2FA in profile
2. Logout and login again
3. Verify OTP sent to email
4. Enter code to complete login
5. Test resend functionality
6. Test expired code handling

---

## 🚨 Troubleshooting

### OTP Not Received
1. Check spam/junk folder
2. Verify Gmail credentials in `.env`
3. Check `two_factor_codes` table for generated codes
4. Review Laravel logs: `storage/logs/laravel.log`
5. Test email with: `php artisan tinker` then `Mail::raw('Test', function($m) { $m->to('test@gmail.com')->subject('Test'); });`

### Account Locked
1. Check `locked_until` in users table
2. Manually unlock:
   ```sql
   UPDATE users SET locked_until = NULL, failed_login_attempts = 0 WHERE email = 'user@gmail.com';
   ```
3. Or wait 30 minutes for automatic unlock

### Session Issues
1. Clear sessions: `php artisan session:clear`
2. Regenerate key: `php artisan key:generate`
3. Clear cache: `php artisan cache:clear`
4. Check database sessions table

---

## 📝 API Endpoints

### Two-Factor Authentication
- `GET /two-factor-challenge` - Show 2FA form
- `POST /two-factor-challenge` - Verify OTP
- `POST /two-factor-challenge/resend` - Resend OTP
- `POST /user/two-factor-authentication` - Enable 2FA
- `POST /user/two-factor-authentication/confirm` - Confirm enable
- `DELETE /user/two-factor-authentication` - Disable 2FA

---

## 🎯 Future Enhancements

### Planned Features
1. **CAPTCHA Integration** after failed attempts
2. **SMS-based 2FA** as alternative to email
3. **Authenticator App Support** (TOTP)
4. **Security Questions** for account recovery
5. **Login Notifications** via email
6. **Device Management** - trusted devices
7. **IP Whitelisting** for admin accounts
8. **Audit Logs** for all security events

---

## 📞 Support

For security concerns or questions:
- Email: security@baltbep.com
- Review logs: `storage/logs/laravel.log`
- Check documentation: Laravel Security Docs

---

## ✅ Security Checklist

- [x] Strong password policy enforced
- [x] Argon2id/Bcrypt password hashing
- [x] Multi-factor authentication (Email OTP)
- [x] Account lockout after 3 failed attempts
- [x] Rate limiting on login attempts
- [x] Secure session management
- [x] Encrypted session data
- [x] HTTP-only cookies
- [x] Role-based access control
- [x] Gmail-only registration
- [x] Login attempt tracking
- [x] Automatic session expiration
- [x] Session regeneration on login
- [x] CSRF protection
- [x] SQL injection prevention (Eloquent ORM)
- [x] XSS protection (Blade templating)

---

**Last Updated**: October 15, 2025
**Version**: 1.0.0