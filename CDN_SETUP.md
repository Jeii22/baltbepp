# CDN Configuration Guide

## Overview
This guide covers how to integrate a Content Delivery Network (CDN) to improve performance, security, and availability of static assets.

---

## 1. Recommended CDN Providers

### Free Options
- **Cloudflare** (Recommended for most use cases)
  - Free SSL/TLS
  - DDoS protection
  - Global CDN
  - WAF (Web Application Firewall)
  
- **jsDelivr** (For open-source libraries only)
  - Already in use for SweetAlert2
  - Free, fast, reliable

### Paid Options
- **AWS CloudFront** (Scalable, integrates with AWS)
- **Bunny CDN** (Budget-friendly, fast)
- **KeyCDN** (Pay-as-you-go)
- **Fastly** (Enterprise-grade)

---

## 2. Cloudflare Setup (Recommended)

### Step 1: Sign Up
1. Visit https://dash.cloudflare.com/sign-up
2. Add your domain (e.g., baltbep.net)
3. Select Free plan

### Step 2: Update DNS
1. Copy Cloudflare nameservers (e.g., `ns1.cloudflare.com`)
2. Update nameservers at your domain registrar
3. Wait for DNS propagation (up to 24 hours)

### Step 3: Configure SSL/TLS
1. Go to SSL/TLS → Overview
2. Select "Full (strict)" mode
3. Enable "Always Use HTTPS"
4. Enable "Automatic HTTPS Rewrites"

### Step 4: Enable Security Features
1. **Firewall Rules:**
   - Block countries (if needed)
   - Challenge suspicious requests
   
2. **Rate Limiting:**
   - Create rule for `/login` (5 requests per minute)
   - Create rule for `/api/*` (100 requests per minute)

3. **WAF (Web Application Firewall):**
   - Enable OWASP Core Ruleset
   - Enable Cloudflare Managed Ruleset

### Step 5: Optimize Performance
1. **Caching:**
   - Go to Caching → Configuration
   - Set Browser Cache TTL to "1 year"
   - Enable "Tiered Cache"

2. **Page Rules:**
   ```
   URL: baltbep.net/build/*
   Settings:
   - Cache Level: Cache Everything
   - Edge Cache TTL: 1 month
   - Browser Cache TTL: 1 year
   ```

3. **Auto Minify:**
   - Enable for JavaScript, CSS, HTML

4. **Brotli Compression:**
   - Enable under Speed → Optimization

### Step 6: Update Laravel Configuration
Update `.env`:
```env
APP_URL=https://baltbep.net
SESSION_SECURE_COOKIE=true
ASSET_URL=https://baltbep.net
```

---

## 3. Self-Hosted CDN with Laravel Mix/Vite

### Option A: Use Laravel Asset Helper
```php
// In Blade templates
<link href="{{ asset('build/app.css') }}" rel="stylesheet">
<script src="{{ asset('build/app.js') }}"></script>
```

### Option B: CDN Subdomain
1. Create subdomain: `cdn.baltbep.net`
2. Point to same server or separate static file server
3. Update `.env`:
   ```env
   ASSET_URL=https://cdn.baltbep.net
   ```

### Option C: Third-Party Storage (AWS S3, DigitalOcean Spaces)
```bash
# Install S3 driver
composer require league/flysystem-aws-s3-v3

# Update config/filesystems.php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
],

# Deploy assets
php artisan storage:link
# Upload build/ folder to S3
```

---

## 4. Update Content Security Policy for CDN

Edit `public/.htaccess`:
```apache
Header always set Content-Security-Policy "default-src 'self'; \
  script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.baltbep.net https://maps.google.com https://maps.googleapis.com; \
  style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.baltbep.net https://fonts.googleapis.com; \
  font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdn.baltbep.net data:; \
  img-src 'self' data: https: blob:; \
  connect-src 'self' https://api.paymongo.com; \
  frame-src https://maps.google.com https://www.google.com; \
  object-src 'none'; \
  base-uri 'self'; \
  form-action 'self';"
```

---

## 5. CDN Cache Busting

### Method 1: Laravel Mix Versioning
```javascript
// webpack.mix.js
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .version(); // Adds hash to filename
```

### Method 2: Query String Versioning
```php
// In Blade
<link href="{{ asset('build/app.css?v=' . config('app.version')) }}" rel="stylesheet">
```

### Method 3: Git Commit Hash
```php
// In AppServiceProvider
public function boot()
{
    view()->share('appVersion', trim(exec('git log --pretty="%h" -n1 HEAD')));
}

// In Blade
<script src="{{ asset('build/app.js?v=' . $appVersion) }}"></script>
```

---

## 6. Testing CDN Configuration

### Check if CDN is Active
```bash
# Check response headers
curl -I https://baltbep.net/build/app.js

# Look for:
# - CF-Cache-Status: HIT (Cloudflare)
# - X-Cache: HIT (Generic CDN)
# - Server: cloudflare
```

### Performance Testing
```bash
# Install
npm install -g lighthouse

# Test with CDN
lighthouse https://baltbep.net --view

# Check metrics:
# - First Contentful Paint (FCP)
# - Largest Contentful Paint (LCP)
# - Total Blocking Time (TBT)
# - Cumulative Layout Shift (CLS)
```

### Load Testing
```bash
# Install Apache Bench
apt-get install apache2-utils

# Test 1000 requests, 100 concurrent
ab -n 1000 -c 100 https://baltbep.net/

# Test static asset
ab -n 5000 -c 200 https://baltbep.net/build/app.js
```

---

## 7. CDN Security Best Practices

1. **Enable Hotlink Protection:**
   - Prevent other sites from embedding your assets
   - Configure in Cloudflare Scrape Shield

2. **Purge Cache on Deploy:**
   ```bash
   # Cloudflare API
   curl -X POST "https://api.cloudflare.com/client/v4/zones/{zone_id}/purge_cache" \
        -H "Authorization: Bearer {api_token}" \
        -H "Content-Type: application/json" \
        --data '{"purge_everything":true}'
   ```

3. **Set Proper CORS Headers:**
   ```apache
   <FilesMatch "\.(ttf|otf|eot|woff|woff2)$">
       Header set Access-Control-Allow-Origin "*"
   </FilesMatch>
   ```

4. **Monitor CDN Logs:**
   - Review Cloudflare Analytics
   - Check for suspicious traffic patterns
   - Set up alerts for DDoS attacks

---

## 8. Current CDN Usage in BaltBep

### Already Using CDN:
```html
<!-- SweetAlert2 -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Google Fonts (acts as CDN) -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Google Maps -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY"></script>
```

### To Be Moved to CDN:
- `/public/build/assets/*` (compiled CSS/JS)
- `/public/images/*` (static images)
- `/storage/app/public/*` (user uploads - optional)

---

## 9. Deployment Checklist

- [ ] Sign up for Cloudflare (or chosen CDN)
- [ ] Update DNS nameservers
- [ ] Enable SSL/TLS (Full strict mode)
- [ ] Configure firewall rules
- [ ] Set up page rules for caching
- [ ] Update `.env` with HTTPS URLs
- [ ] Update CSP headers for CDN domains
- [ ] Test asset loading
- [ ] Enable auto-minification
- [ ] Set up cache purging on deploy
- [ ] Monitor performance improvements

---

## 10. Rollback Plan

If CDN causes issues:

1. **Quick Disable (Cloudflare):**
   - DNS → Click orange cloud icon (turn gray)
   - Traffic bypasses Cloudflare immediately

2. **Revert DNS:**
   - Change nameservers back to original
   - Wait for propagation

3. **Remove ASSET_URL:**
   ```env
   # Comment out or remove
   # ASSET_URL=https://cdn.baltbep.net
   ```

4. **Clear Application Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

---

## Resources

- Cloudflare Docs: https://developers.cloudflare.com/
- Laravel Asset Management: https://laravel.com/docs/mix
- Web Performance Best Practices: https://web.dev/performance/
- CDN Comparison: https://www.cdnplanet.com/compare/
