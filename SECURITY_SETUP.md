# Quick Security Setup Guide

## 🚀 Getting Started

### Step 1: Update Environment Variables

Edit your `.env` file with these security settings:

```env
# Password Hashing
HASH_DRIVER=argon2id
BCRYPT_ROUNDS=12

# Session Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_SECURE_COOKIE=false  # Change to true in production with HTTPS

# Email Configuration for OTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@baltbep.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 2: Configure Gmail for Sending OTP

1. **Enable 2-Step Verification** on your Gmail account:
   - Go to https://myaccount.google.com/security
   - Click "2-Step Verification"
   - Follow the setup process

2. **Generate App Password**:
   - Go to https://myaccount.google.com/apppasswords
   - Select "Mail" as the app
   - Select "Windows Computer" as the device
   - Click "Generate"
   - Copy the 16-character password (e.g., `abcd efgh ijkl mnop`)

3. **Update .env**:
   ```env
   MAIL_USERNAME=youremail@gmail.com
   MAIL_PASSWORD=abcdefghijklmnop  # Remove spaces
   ```

### Step 3: Run Migrations

The migrations have already been run, but if you need to run them again:

```bash
php artisan migrate
```

This creates:
- Security fields in `users` table
- `two_factor_codes` table for OTP storage

### Step 4: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 5: Test the System

#### Test Registration
1. Go to login page
2. Click "Register" button
3. Fill in the form with:
   - Name: Test User
   - Email: testuser@gmail.com (must be Gmail)
   - Password: Test@123 (meets all requirements)
   - Confirm Password: Test@123
   - Check "Terms & Conditions"
4. Click "Create account"
5. Check email for OTP
6. Enter OTP to verify

#### Test Login with 2FA
1. Enable 2FA in Profile → Security Settings
2. Logout
3. Login with credentials
4. Check email for OTP
5. Enter OTP to complete login

#### Test Account Lockout
1. Try logging in with wrong password 3 times
2. Account should lock for 30 minutes
3. Verify error message

---

## 🔧 Configuration Options

### Adjust Lockout Settings

Edit `app/Models/User.php`:

```php
// Change lockout duration (default: 30 minutes)
public function lockAccount(int $minutes = 30): void
{
    $this->update([
        'locked_until' => now()->addMinutes($minutes),
    ]);
}

// Change failed attempts threshold (default: 3)
public function incrementFailedAttempts(): void
{
    $this->increment('failed_login_attempts');
    
    if ($this->failed_login_attempts >= 3) {  // Change this number
        $this->lockAccount(30);
    }
}
```

### Adjust OTP Expiration

Edit `app/Models/TwoFactorCode.php`:

```php
public static function createForUser(User $user, string $type = 'login'): self
{
    return self::create([
        'user_id' => $user->id,
        'code' => self::generateCode(),
        'type' => $type,
        'expires_at' => Carbon::now()->addMinutes(10),  // Change this
    ]);
}
```

### Adjust Session Lifetime

Edit `.env`:

```env
SESSION_LIFETIME=120  # Change to desired minutes
```

---

## 🎯 Testing Checklist

- [ ] Registration with Gmail works
- [ ] Registration with non-Gmail fails
- [ ] Weak passwords are rejected
- [ ] Strong passwords are accepted
- [ ] OTP email is received
- [ ] OTP verification works
- [ ] OTP expiration works (after 10 minutes)
- [ ] Resend OTP works
- [ ] Login without 2FA works
- [ ] Login with 2FA works
- [ ] 3 failed logins lock account
- [ ] Locked account shows error message
- [ ] Account unlocks after 30 minutes
- [ ] Enable 2FA from profile works
- [ ] Disable 2FA requires password
- [ ] Session expires after 2 hours
- [ ] Logout clears session
- [ ] Role-based access works

---

## 🐛 Common Issues & Solutions

### Issue: OTP Email Not Received

**Solution 1**: Check Gmail App Password
```bash
# Test email sending
php artisan tinker
>>> Mail::raw('Test email', function($m) { $m->to('your-email@gmail.com')->subject('Test'); });
```

**Solution 2**: Check Spam Folder
- OTP emails might be in spam
- Mark as "Not Spam" to whitelist

**Solution 3**: Check Logs
```bash
# View Laravel logs
tail -f storage/logs/laravel.log
```

### Issue: Account Locked Permanently

---

## 🌐 Web Server Hardening: Disable Directory Listing

Directory listing is disabled to prevent users from browsing URL folders:

- Global: `Options -Indexes` is set in both root `.htaccess` and `public/.htaccess`.
- Public subfolders also have their own `.htaccess` to enforce this, even if host overrides differ:
    - `public/images/.htaccess`
    - `public/build/.htaccess`
    - `public/storage/.htaccess`
    - `public/payment_qr_codes/.htaccess`

Additionally, direct access to front controllers is blocked with 403:

- Root `.htaccess`: blocks `/public/index.php`
- `public/.htaccess`: blocks `/index.php`

Quick tests:

1. Open `/public/index.php` → 403 Forbidden
2. Open `/index.php` (if the docroot is `public`) → 403 Forbidden
3. Open `/images/` or `/build/` → no listing (403)
4. Open a real asset like `/build/assets/app.css` → loads normally

Note: If your hosting disables `AllowOverride Options`, folder `.htaccess` files may be ignored. Ask your host to enable it or configure `Options -Indexes` in the vhost config.

**Solution**: Manually unlock via database
```sql
UPDATE users 
SET locked_until = NULL, failed_login_attempts = 0 
WHERE email = 'user@gmail.com';
```

Or via Tinker:
```bash
php artisan tinker
>>> $user = User::where('email', 'user@gmail.com')->first();
>>> $user->unlockAccount();
```

### Issue: Session Not Working

**Solution**: Clear and regenerate
```bash
php artisan session:clear
php artisan config:clear
php artisan cache:clear
```

### Issue: Argon2id Not Available

**Solution**: Check PHP installation
```bash
php -i | grep -i argon
```

If not available, it will automatically fall back to Bcrypt.

To install Argon2:
- Windows: Enable in `php.ini` (usually already enabled)
- Linux: `sudo apt-get install php-sodium`

---

## 📊 Monitoring

### Check Failed Login Attempts

```sql
SELECT * FROM login_attempts 
WHERE successful = 0 
ORDER BY attempted_at DESC 
LIMIT 20;
```

### Check Locked Accounts

```sql
SELECT id, email, failed_login_attempts, locked_until 
FROM users 
WHERE locked_until IS NOT NULL 
AND locked_until > NOW();
```

### Check 2FA Adoption

```sql
SELECT 
    COUNT(*) as total_users,
    SUM(two_factor_enabled) as users_with_2fa,
    ROUND(SUM(two_factor_enabled) / COUNT(*) * 100, 2) as adoption_rate
FROM users;
```

### Check Active Sessions

```sql
SELECT COUNT(*) as active_sessions 
FROM sessions 
WHERE last_activity > UNIX_TIMESTAMP(NOW() - INTERVAL 2 HOUR);
```

---

## 🔐 Security Recommendations

### For Development
- Keep `SESSION_SECURE_COOKIE=false`
- Use `MAIL_MAILER=log` to test without sending emails
- Monitor `storage/logs/laravel.log`

### For Production
1. **Enable HTTPS** and update:
   ```env
   SESSION_SECURE_COOKIE=true
   SESSION_SAME_SITE=strict
   ```

2. **Use Queue for Emails**:
   ```env
   QUEUE_CONNECTION=database
   ```
   Then run: `php artisan queue:work`

3. **Enable Rate Limiting** on all routes

4. **Regular Backups** of database

5. **Monitor Logs** for suspicious activity

6. **Update Dependencies** regularly:
   ```bash
   composer update
   ```

7. **Consider Making 2FA Mandatory** for admin accounts

---

## 📞 Need Help?

- Check `SECURITY_FEATURES.md` for detailed documentation
- Review Laravel logs: `storage/logs/laravel.log`
- Check database tables: `users`, `two_factor_codes`, `login_attempts`
- Test email configuration with `php artisan tinker`

---

**Setup Complete!** 🎉

Your application now has:
- ✅ Strong password enforcement
- ✅ Multi-factor authentication
- ✅ Account lockout protection
- ✅ Secure session management
- ✅ Role-based access control
- ✅ Gmail-only registration