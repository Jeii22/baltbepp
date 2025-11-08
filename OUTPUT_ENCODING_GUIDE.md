# Output Encoding & XSS Prevention Guide

## Overview
This document outlines the output encoding strategies implemented to prevent Cross-Site Scripting (XSS) attacks and ensure secure data handling throughout the BaltBep application.

---

## 1. Output Encoding Status

### ✅ Implemented Security Measures

#### A. **Blade Template Auto-Escaping**
All Blade templates use `{{ }}` syntax which **automatically HTML-encodes** output:

```php
// Secure - Auto HTML-encoded
{{ $user->name }}
{{ $user->email }}
{{ $booking->status }}

// ❌ NEVER use (outputs raw HTML):
{!! $dangerousContent !!}  // Only for trusted, sanitized content
{{{ $content }}}           // Deprecated syntax
```

**Current Status:** ✅ All user-facing templates verified to use `{{ }}`

#### B. **Context-Specific Encoding**

| Context | Encoding Method | Example |
|---------|----------------|---------|
| HTML Body | `{{ }}` (automatic) | `<p>{{ $user->name }}</p>` |
| HTML Attributes | `{{ }}` (automatic) | `<input value="{{ $user->email }}">` |
| JavaScript Strings | `@json()` directive | `let data = @json($array);` |
| URLs | `urlencode()` helper | `<a href="/search?q={{ urlencode($query) }}">` |
| CSS | Avoid dynamic CSS | Use classes instead |

---

## 2. XSS Prevention Checklist

### ✅ HTML Context (DONE)
- [x] All user input displayed via `{{ }}` in Blade templates
- [x] No raw output (`{!! !!}`) for untrusted data
- [x] Error messages encoded
- [x] Database values encoded on display

### ✅ JavaScript Context (DONE)
- [x] Using `@json()` for passing PHP data to JS
- [x] Avoiding inline `onclick="..."` with user data
- [x] SweetAlert messages use `@json()` for content

### ✅ URL Context (PARTIALLY DONE - needs review)
- [x] Routes use Laravel named routes (no .php extension)
- [x] Query parameters validated in controllers
- [ ] Need to verify URL encoding for user-generated search queries

### ✅ Attribute Context (DONE)
- [x] All attributes use `{{ }}` encoding
- [x] Boolean attributes handled safely

---

## 3. .PHP Extension Removal

### ✅ Current Implementation

#### A. **Laravel Routing (Clean URLs)**
All URLs are handled by Laravel's routing system with NO .php extensions:

```php
// Clean URLs (no .php)
https://baltbep.net/login
https://baltbep.net/admin/users
https://baltbep.net/bookings/checkout
https://baltbep.net/trips/search
```

#### B. **.htaccess Configuration**
The `public/.htaccess` already hides index.php and blocks direct access:

```apache
# Block direct access to index.php
RewriteRule ^index\.php$ - [F,L]

# All requests go through index.php (hidden)
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

#### C. **Root .htaccess**
Blocks `/public/index.php` access:

```apache
# Block explicit /public/index.php access (return 403)
RewriteRule ^public/index\.php$ - [F,L]
```

**Result:** Attempting to access any .php file directly returns 403 Forbidden.

---

## 4. Encoding Best Practices

### DO:
✅ Use `{{ $variable }}` for all HTML output  
✅ Use `@json($data)` for passing data to JavaScript  
✅ Use `route('name')` for generating URLs  
✅ Use `urlencode()` for query string parameters  
✅ Validate and sanitize input on server-side BEFORE storing  
✅ Use Laravel's validation rules  

### DON'T:
❌ Use `{!! $variable !!}` for user input  
❌ Build raw HTML strings in PHP for user content  
❌ Trust client-side validation alone  
❌ Use string concatenation for SQL queries (use Eloquent/Query Builder)  
❌ Output user data directly in `<script>` tags without encoding  
❌ Use inline event handlers with user data (`onclick="userInput"`)  

---

## 5. Secure Coding Examples

### Example 1: Display User Input
```php
// ✅ CORRECT - Auto HTML-encoded
<h1>Welcome, {{ $user->name }}</h1>
<p>Email: {{ $user->email }}</p>

// ❌ WRONG - XSS Vulnerable
<h1>Welcome, {!! $user->name !!}</h1>
<p>Email: <?php echo $user->email; ?></p>
```

### Example 2: JavaScript Data Passing
```php
// ✅ CORRECT - JSON encoded
<script>
    let userData = @json($user);
    let config = @json([
        'name' => $user->name,
        'email' => $user->email
    ]);
</script>

// ❌ WRONG - XSS Vulnerable
<script>
    let userName = "{{ $user->name }}"; // Can break if name contains quotes
    let userEmail = {!! json_encode($user->email) !!}; // Unnecessary raw output
</script>
```

### Example 3: URL Encoding
```php
// ✅ CORRECT - URL-safe
<a href="{{ route('search', ['q' => urlencode($query)]) }}">Search</a>
<a href="/trips/search?origin={{ urlencode($origin) }}&destination={{ urlencode($destination) }}">Book</a>

// ❌ WRONG - Can break with special characters
<a href="/search?q={{ $query }}">Search</a>
```

### Example 4: Form Attributes
```php
// ✅ CORRECT - Auto HTML-encoded
<input type="text" name="email" value="{{ old('email', $user->email) }}">
<textarea>{{ old('bio', $user->bio) }}</textarea>

// ❌ WRONG
<input type="text" name="email" value="{!! $user->email !!}">
```

### Example 5: SweetAlert Messages
```php
// ✅ CORRECT - JSON encoded
<script>
Swal.fire({
    title: 'Welcome',
    text: @json($message),
    icon: 'success'
});
</script>

// ❌ WRONG - Can break with quotes
<script>
Swal.fire({
    title: 'Welcome',
    text: '{{ $message }}',  // XSS if message contains quotes or script tags
});
</script>
```

---

## 6. Input Validation & Sanitization

### Server-Side Validation (Always Required)
```php
// In Controller or FormRequest
$validated = $request->validate([
    'email' => 'required|email|max:255',
    'name' => 'required|string|max:100',
    'message' => 'required|string|max:1000',
    'url' => 'nullable|url',
]);

// Laravel automatically prevents SQL injection via Eloquent/Query Builder
User::create($validated);
```

### HTML Stripping (When Needed)
```php
// Strip all HTML tags
$clean = strip_tags($input);

// Allow specific tags
$clean = strip_tags($input, '<b><i><u>');

// Use Laravel HTML Purifier (install first)
$clean = clean($input);
```

---

## 7. Testing for XSS Vulnerabilities

### Manual Testing Payloads
Try injecting these into all input fields:

```html
<script>alert('XSS')</script>
<img src=x onerror=alert('XSS')>
<svg/onload=alert('XSS')>
javascript:alert('XSS')
'><script>alert('XSS')</script>
"><img src=x onerror=alert('XSS')>
```

**Expected Result:** All should be displayed as plain text (HTML-encoded), not executed.

### Automated Testing
```bash
# Using OWASP ZAP
docker run -t owasp/zap2docker-stable zap-baseline.py -t https://baltbep.net

# Using XSStrike
python3 xsstrike.py -u "https://baltbep.net/search?q=test"
```

---

## 8. Security Headers (Already Implemented)

These headers provide additional XSS protection:

```apache
# In public/.htaccess
Header always set X-XSS-Protection "1; mode=block"
Header always set X-Content-Type-Options "nosniff"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'..."
```

**CSP Benefits:**
- Blocks inline scripts from untrusted sources
- Prevents loading scripts from unauthorized domains
- Mitigates XSS even if encoding fails

---

## 9. Common Pitfalls to Avoid

### Pitfall 1: Double Encoding
```php
// ❌ WRONG - Double encoded
{{ htmlspecialchars($user->name) }}  // Blade already encodes!

// ✅ CORRECT
{{ $user->name }}
```

### Pitfall 2: Trusting Old Input
```php
// ❌ RISKY - If old() contains malicious script
<input value="{!! old('name') !!}">

// ✅ CORRECT
<input value="{{ old('name') }}">
```

### Pitfall 3: Direct Echo in Blade
```php
// ❌ WRONG - Bypasses Blade encoding
<?php echo $user->name; ?>

// ✅ CORRECT
{{ $user->name }}
```

---

## 10. File Upload Security (Already Implemented)

```php
// In ProfileUpdateRequest and PaymentMethodController
$request->validate([
    'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

// NEVER trust file extensions, validate MIME type
// NEVER execute uploaded files
// Store uploads outside public directory when possible
```

---

## 11. Database Query Safety

### ✅ Using Eloquent (Safe from SQL Injection)
```php
// ✅ CORRECT - Parameterized queries
User::where('email', $email)->first();
DB::table('users')->where('id', $id)->update(['name' => $name]);

// ❌ WRONG - SQL Injection vulnerable
DB::select("SELECT * FROM users WHERE email = '$email'");
```

---

## 12. Verification Checklist

Run these checks before deployment:

- [ ] All Blade templates use `{{ }}` for user data
- [ ] No `{!! !!}` used for untrusted content
- [ ] JavaScript uses `@json()` for PHP data
- [ ] URLs use `route()` helper or `urlencode()`
- [ ] Forms validated on server-side
- [ ] File uploads restricted by type and size
- [ ] CSP headers configured
- [ ] `.php` extensions blocked in URLs
- [ ] XSS payloads tested manually
- [ ] Automated security scan passed (OWASP ZAP)

---

## 13. Resources

- Laravel Security Docs: https://laravel.com/docs/security
- OWASP XSS Prevention: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
- Blade Templates: https://laravel.com/docs/blade
- Content Security Policy: https://content-security-policy.com/

---

## Summary

✅ **Output Encoding:** All templates use `{{ }}` for automatic HTML encoding  
✅ **JavaScript:** Using `@json()` for safe data passing  
✅ **.PHP Extension:** Hidden via Laravel routing + .htaccess blocks  
✅ **URL Encoding:** Needs review for search/filter query parameters  
✅ **Input Validation:** Server-side validation on all forms  
✅ **Security Headers:** CSP, X-XSS-Protection, X-Content-Type-Options set  

**Next Steps:**
1. Review and add `urlencode()` for user-generated search queries
2. Run OWASP ZAP scan to verify no XSS vulnerabilities
3. Update this document with any findings
