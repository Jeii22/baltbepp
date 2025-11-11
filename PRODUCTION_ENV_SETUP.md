# Production Environment Setup Guide

## Critical Production .env Settings

Copy these settings to your production `.env` file (replace `baltbep.net` with your actual domain):

```env
# Application
APP_NAME="Balt Bep"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://baltbep.net

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db_name
DB_USERNAME=your_production_db_user
DB_PASSWORD=your_production_db_password

# Session Configuration (CRITICAL FOR LOGIN)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.baltbep.net
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@baltbep.net
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@baltbep.net
MAIL_FROM_NAME="${APP_NAME}"

# Security
IDLE_TIMEOUT_MINUTES=30

# PayMongo (if using payment gateway)
PAYMONGO_SECRET_KEY=your_paymongo_secret_key
PAYMONGO_PUBLIC_KEY=your_paymongo_public_key

# Google OAuth (if using social login)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://baltbep.net/auth/google/callback
```

## Deployment Steps

1. **Pull latest code:**
   ```bash
   cd /path/to/your/app
   git pull origin main
   ```

2. **Update dependencies (if composer.json changed):**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Clear all caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Run migrations (if needed):**
   ```bash
   php artisan migrate --force
   ```

5. **Optimize for production:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Set proper permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

## Troubleshooting Login Issues

### Can't login - Session not persisting

**Check:**
1. Ensure `SESSION_DOMAIN` matches your domain (use `.baltbep.net` for all subdomains)
2. Ensure `SESSION_SECURE_COOKIE=true` for HTTPS sites
3. Ensure `SESSION_SAME_SITE=lax` (not strict)
4. Database `sessions` table exists and is writable
5. Storage folder has write permissions

**Test session:**
```bash
php artisan tinker
```
Then run:
```php
session()->put('test', 'value');
session()->get('test');
```

### Redirect loops

**Check:**
1. `.htaccess` has proxy-aware HTTPS redirect (already fixed)
2. `APP_URL` matches your actual domain
3. Trust proxies is enabled in `bootstrap/app.php` (already fixed)

### 500 Errors

**Check logs:**
```bash
tail -f storage/logs/laravel.log
```

**Enable debug temporarily (ONLY for testing, disable after):**
```env
APP_DEBUG=true
```

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated (`php artisan key:generate`)
- [ ] Database credentials secured
- [ ] `.env` file has proper permissions (chmod 600)
- [ ] SSL certificate installed and valid
- [ ] CORS headers configured properly
- [ ] Rate limiting enabled
- [ ] Two-factor authentication working
