# Security Testing Guide

## Overview
Regular security testing is critical to maintain the security posture of the BaltBep application. This guide covers manual testing, automated tools, and best practices.

---

## 1. Manual Security Testing Checklist

### Authentication & Authorization
- [ ] Test failed login attempts (3 strikes = account lock)
- [ ] Verify account lockout expires after 30 minutes
- [ ] Test 2FA bypass attempts
- [ ] Test password reset flow with expired tokens
- [ ] Verify role-based access control (user/admin/superadmin)
- [ ] Test session timeout (120 minutes + idle timeout)
- [ ] Verify logout clears session completely
- [ ] Test Google OAuth flow

### Input Validation
- [ ] SQL injection attempts in search/filter fields
- [ ] XSS attempts in user input fields
- [ ] File upload validation (2MB limit, allowed types)
- [ ] Test special characters in forms
- [ ] Test extremely long inputs (buffer overflow)

### Security Headers
- [ ] Verify X-Frame-Options: DENY
- [ ] Check Content-Security-Policy
- [ ] Verify HSTS header (on HTTPS)
- [ ] Check X-Content-Type-Options: nosniff
- [ ] Verify Permissions-Policy restrictions

### Directory & File Access
- [ ] Try accessing /vendor, /config, /app, /.env directly
- [ ] Test directory listing on /public/images, /public/build
- [ ] Verify /public/index.php returns 403
- [ ] Test access to .git, .htaccess files

### Activity Logging
- [ ] Verify admin actions are logged
- [ ] Check user account views are logged
- [ ] Verify logs contain: action, user, IP, timestamp, metadata
- [ ] Check security.log file rotation (90 days)

---

## 2. Automated Security Testing Tools

### A. OWASP ZAP (Zed Attack Proxy)
```bash
# Install ZAP
# Download from: https://www.zaproxy.org/download/

# Run baseline scan
docker run -t owasp/zap2docker-stable zap-baseline.py -t http://your-domain.com

# Run full scan (requires authentication)
docker run -t owasp/zap2docker-stable zap-full-scan.py -t http://your-domain.com
```

**What to check:**
- SQL injection vulnerabilities
- XSS vulnerabilities
- CSRF token validation
- Security headers
- Cookie security

### B. Nikto Web Scanner
```bash
# Install
apt-get install nikto

# Run scan
nikto -h http://your-domain.com -ssl

# Save report
nikto -h http://your-domain.com -output report.html -Format html
```

### C. SQLMap (SQL Injection)
```bash
# Install
pip install sqlmap

# Test search endpoint
sqlmap -u "http://your-domain.com/trips/search?origin=test" --batch --risk=3

# Test with authentication
sqlmap -u "http://your-domain.com/admin/users" --cookie="session_cookie_here" --batch
```

### D. Burp Suite Community
- Download: https://portswigger.net/burp/communitydownload
- Use for: Manual penetration testing, session analysis, parameter fuzzing

### E. Lighthouse Security Audit
```bash
# Install
npm install -g lighthouse

# Run audit
lighthouse http://your-domain.com --only-categories=best-practices --view

# Check for:
# - HTTPS usage
# - Security headers
# - Mixed content
# - Vulnerable libraries
```

---

## 3. Laravel-Specific Testing

### A. Run Laravel Security Checker
```bash
# Install
composer require --dev enlightn/security-checker

# Run scan
php artisan security-check

# Check for known vulnerabilities in dependencies
```

### B. Laravel Enlightn (Advanced Security Auditing)
```bash
# Install
composer require --dev enlightn/enlightn

# Run security audit
php artisan enlightn

# Generate report
php artisan enlightn --report
```

### C. PHP Security Checker
```bash
# Install
composer require --dev sensiolabs/security-checker

# Check composer.lock for vulnerabilities
php vendor/bin/security-checker security:check
```

---

## 4. Penetration Testing Scenarios

### Test 1: Session Hijacking
1. Login as user A
2. Copy session cookie
3. Open incognito window, inject cookie
4. Try accessing protected routes
**Expected:** Session should be invalidated or tied to IP/User-Agent

### Test 2: CSRF Attack
1. Create external form posting to `/bookings/process`
2. Submit while logged in
**Expected:** 419 CSRF token mismatch error

### Test 3: File Upload Bypass
1. Try uploading .php file renamed as .jpg
2. Try uploading file > 2MB
3. Try uploading with double extension (file.php.jpg)
**Expected:** Validation errors, no execution

### Test 4: Privilege Escalation
1. Login as regular user
2. Manually visit `/admin/dashboard`, `/superadmin/dashboard`
3. Try accessing `/users`, `/fares`, `/settings`
**Expected:** 403 Forbidden or redirect

### Test 5: Rate Limiting (if implemented)
1. Make 100 rapid requests to `/login`
2. Check for 429 Too Many Requests
**Expected:** Rate limit after threshold

---

## 5. Production Security Checklist

Before deploying to production:

- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production`
- [ ] Enable HTTPS and force SSL redirect
- [ ] Set `SESSION_SECURE_COOKIE=true`
- [ ] Review and tighten CSP headers
- [ ] Enable rate limiting on sensitive routes
- [ ] Set up automated backup for database
- [ ] Configure fail2ban for brute force protection
- [ ] Enable server firewall (UFW/iptables)
- [ ] Set up SSL with Let's Encrypt
- [ ] Configure log rotation
- [ ] Review all admin accounts (disable/delete test accounts)
- [ ] Set strong database password
- [ ] Disable phpMyAdmin or secure with .htpasswd
- [ ] Run final security scan before launch

---

## 6. Continuous Security Monitoring

### Daily
- Review `storage/logs/security.log` for suspicious activity
- Check failed login attempts
- Monitor locked accounts

### Weekly
- Run automated security scan (ZAP/Nikto)
- Review admin activity logs
- Check for new CVEs in dependencies (`composer audit`)

### Monthly
- Full penetration test
- Review and update security policies
- Audit user permissions and roles
- Check for outdated packages (`composer outdated`)

### Quarterly
- Third-party security audit (recommended)
- Review and update CSP
- Rotate secrets and API keys
- Update SSL certificates

---

## 7. Reporting Security Issues

If you discover a security vulnerability:

1. **Do NOT** open a public GitHub issue
2. Email security team: security@baltbep.net
3. Include:
   - Description of vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (optional)

---

## 8. Security Testing Resources

- OWASP Top 10: https://owasp.org/www-project-top-ten/
- Laravel Security Best Practices: https://laravel.com/docs/security
- PHP Security Guide: https://phpsecurity.readthedocs.io/
- Web Security Testing Guide: https://owasp.org/www-project-web-security-testing-guide/

---

## Notes

- Always test on staging environment first
- Back up database before running destructive tests
- Use VPN or authorized IPs when running automated scans
- Document all findings and remediation steps
- Keep this guide updated as new threats emerge
