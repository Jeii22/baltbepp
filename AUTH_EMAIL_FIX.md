# Authentication & Email Fix Summary

## Issues Found & Fixed

### 1. **Missing 2FA Routes** ❌ → ✅
**Problem:** After login, the app tried to redirect to `two-factor.login` route but it didn't exist in `routes/auth.php`, causing a 404 error.

**Fixed:** Added the missing routes:
- `GET /two-factor-challenge` - Show OTP verification page
- `POST /two-factor-challenge` - Verify OTP code
- `POST /two-factor-resend` - Resend OTP email

### 2. **Wrong Session Driver** ❌ → ✅
**Problem:** `.env.production` had `SESSION_DRIVER=file` which doesn't work reliably across multiple requests in production.

**Fixed:** Changed to `SESSION_DRIVER=database` for persistent sessions.

### 3. **Insecure Session Settings** ❌ → ✅
**Problem:** 
- `SESSION_SECURE_COOKIE=false` - Cookies sent over HTTP (insecure)
- `SESSION_DOMAIN=null` - Cookies not shared across subdomains

**Fixed:**
- `SESSION_SECURE_COOKIE=true` - Requires HTTPS
- `SESSION_DOMAIN=.baltbep.com` - Works for www and non-www

### 4. **Mail Not Configured** ❌ → ✅
**Problem:** `MAIL_MAILER=log` means emails are only written to log files, not actually sent!

**Fixed:** Changed to `MAIL_MAILER=smtp` with Hostinger SMTP settings.

### 5. **Wrong Queue Driver** ❌ → ✅
**Problem:** `QUEUE_CONNECTION=sync` processes jobs immediately, blocking requests.

**Fixed:** Changed to `QUEUE_CONNECTION=database` for background processing.

### 6. **Missing reCAPTCHA Config** ❌ → ✅
**Problem:** No reCAPTCHA keys in production .env

**Fixed:** Added placeholder keys (you need to add real keys).

---

## 🚀 Critical Production Setup Steps

### Step 1: Update Production .env File

**IMPORTANT:** You need to manually set these values on your production server:

```env
# Email Configuration (REQUIRED for OTP to work!)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@baltbep.com
MAIL_PASSWORD=YOUR_ACTUAL_EMAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@baltbep.com

# reCAPTCHA (REQUIRED for login/register!)
RECAPTCHA_PUBLIC_KEY=YOUR_RECAPTCHA_SITE_KEY
RECAPTCHA_SECRET_KEY=YOUR_RECAPTCHA_SECRET_KEY
```

### Step 2: Deploy to Production

```bash
# 1. Pull latest code
cd /path/to/your/app
git pull origin main

# 2. Update .env file with real credentials
nano .env  # or use your hosting file manager
# Set MAIL_PASSWORD and RECAPTCHA keys

# 3. Run migrations (ensure sessions table exists)
php artisan migrate --force

# 4. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Re-cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📧 Email Setup Guide

### Option 1: Hostinger Email (Recommended)

1. **Create email account:**
   - Login to Hostinger
   - Go to Email → Create new email: `noreply@baltbep.com`
   - Set a strong password

2. **Get SMTP settings:**
   - Host: `smtp.hostinger.com`
   - Port: `587`
   - Encryption: `TLS`

3. **Update .env:**
   ```env
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@baltbep.com
   MAIL_PASSWORD=your_email_password
   MAIL_ENCRYPTION=tls
   ```

### Option 2: Gmail SMTP (Alternative)

1. **Enable 2FA on Gmail**
2. **Create App Password:**
   - Go to https://myaccount.google.com/apppasswords
   - Generate password for "Mail"

3. **Update .env:**
   ```env
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   ```

---

## 🔐 reCAPTCHA Setup

1. **Get Keys:**
   - Visit https://www.google.com/recaptcha/admin
   - Register domain: `baltbep.com`
   - Choose reCAPTCHA v3
   - Get Site Key and Secret Key

2. **Update .env:**
   ```env
   RECAPTCHA_PUBLIC_KEY=6Lc...your_site_key
   RECAPTCHA_SECRET_KEY=6Lc...your_secret_key
   RECAPTCHA_VERSION=v3
   RECAPTCHA_SCORE_THRESHOLD=0.5
   ```

---

## ✅ Testing the Fix

### Test Login Flow:

1. **Visit:** https://www.baltbep.com/login
2. **Enter credentials** and click Login
3. **Should redirect to:** `/two-factor-challenge` (OTP page)
4. **Check email** for 6-digit verification code
5. **Enter code** and verify
6. **Should redirect to:** Dashboard

### Test Email:

```bash
# On production server, run tinker:
php artisan tinker
```

Then test:
```php
Mail::raw('Test email from BaltBep', function($msg) {
    $msg->to('your-email@example.com')
        ->subject('Test Email');
});
```

Check if email arrives. If not, check `storage/logs/laravel.log` for errors.

---

## 🐛 Troubleshooting

### "Route [two-factor.login] not defined"
- Make sure you pulled latest code (`git pull`)
- Clear route cache: `php artisan route:clear`
- Re-cache: `php artisan route:cache`

### Emails not sending
1. Check `storage/logs/laravel.log` for SMTP errors
2. Verify email credentials are correct
3. Test with tinker (see above)
4. Check spam folder
5. Try Gmail SMTP as alternative

### Session not persisting / Can't login
1. Ensure `sessions` table exists: `php artisan migrate`
2. Check database connection in `.env`
3. Verify `SESSION_DRIVER=database`
4. Clear config cache: `php artisan config:clear`

### reCAPTCHA errors
1. Verify keys are for correct domain
2. Check browser console for errors
3. Ensure `config:cache` was run after updating keys

---

## 📝 Complete Production .env Checklist

Make sure your production `.env` has:

- [x] `APP_URL=https://www.baltbep.com` (not http!)
- [x] `APP_DEBUG=false`
- [x] `SESSION_DRIVER=database`
- [x] `SESSION_SECURE_COOKIE=true`
- [x] `SESSION_DOMAIN=.baltbep.com`
- [x] `QUEUE_CONNECTION=database`
- [x] `MAIL_MAILER=smtp` (not log!)
- [ ] `MAIL_PASSWORD` set to real password
- [ ] `RECAPTCHA_PUBLIC_KEY` set to real key
- [ ] `RECAPTCHA_SECRET_KEY` set to real key

---

## What Changed in Code

**Files Modified:**

1. **routes/auth.php**
   - Added TwoFactorController import
   - Added 3 two-factor routes (show, verify, resend)

2. **.env.production**
   - Fixed session driver and settings
   - Fixed mail configuration
   - Added reCAPTCHA placeholders
   - Fixed Google OAuth redirect URL

**No other code changes needed** - the controllers and views already existed!

---

After deploying and setting the email + reCAPTCHA credentials, login will work perfectly:
1. User enters credentials → Login page
2. Credentials validated → 2FA page
3. OTP email sent
4. User enters code → Dashboard ✅
