# BaltBep – Repository Quick Reference

This file summarizes the project’s tech stack, setup steps, useful commands, and common troubleshooting for fast onboarding.

## Project Overview
- **Framework**: Laravel ^12.0 (PHP ^8.2)
- **Auth Starter**: Laravel Breeze (dev dependency present)
- **Frontend**: Vite ^7, Tailwind CSS ^3, Alpine.js, Axios, laravel-vite-plugin
- **Testing**: Pest + Laravel plugin
- **Queues/Sessions/Cache**: Database drivers configured in .env.example
- **Default DB in .env.example**: SQLite (DB_CONNECTION=sqlite)

## Directory Highlights
- **app/Http/Controllers**: BookingController, FareController, TripController, UserController, etc.
- **app/Models**: User, Trip
- **database/migrations**:
  - 0001_01_01_000000_create_users_table
  - 0001_01_01_000001_create_cache_table
  - 0001_01_01_000002_create_jobs_table
  - 2025_08_23_080651_add_role_to_users_table
  - 2025_08_26_075723_create_trips_table
- **resources/views**: welcome.blade.php, dashboard.blade.php, views for trips, components, layouts
- **routes/web.php**: main routes, auth routes, superadmin group

## Routes Summary (web.php)
- `GET /` → WelcomeController@index (note: `/` is also defined earlier as `view('welcome')`; last definition wins)
- `GET /dashboard` → view('dashboard') with middleware: `auth`, `verified`
- `GET /superadmin/dashboard` → view('superadmin.dashboard')
- Superadmin-only group (requires `auth`, `isSuperAdmin`):
  - `/users` → UserController@index
  - `/trips` → TripController@index
  - `/fares` → FareController@index
  - `/bookings` → BookingController@index
  - `/reports` → ReportController@index
  - `/settings` → SettingController@index
- Auth group: `Route::resource('trips', TripController::class)`
- `require __DIR__/auth.php` for auth routes

If `isSuperAdmin` middleware is missing, create and register it in app/Http/Kernel.php.

## Setup (Windows/XAMPP, PowerShell)
1) Go to project
```powershell
Set-Location "c:\xampp\htdocs\hatdog\BaltBep"
```

2) Install PHP dependencies
```powershell
composer install
```

3) Create .env and app key
```powershell
Copy-Item ".env.example" ".env" -Force
php artisan key:generate
```

4) Choose database option
- SQLite (fastest to start; matches .env.example):
  ```powershell
  New-Item -ItemType File -Path "database\database.sqlite" -Force | Out-Null
  # Ensure in .env: DB_CONNECTION=sqlite and other DB_* vars are commented
  php artisan migrate --seed
  ```
- MySQL (via XAMPP):
  ```powershell
  # Create a database in phpMyAdmin, e.g., baltbep
  # Then set these in .env:
  # DB_CONNECTION=mysql
  # DB_HOST=127.0.0.1
  # DB_PORT=3306
  # DB_DATABASE=baltbep
  # DB_USERNAME=root
  # DB_PASSWORD=
  php artisan migrate --seed
  ```

5) Link storage and clear caches
```powershell
php artisan storage:link
php artisan optimize:clear
```

6) Install JS dependencies and build assets
```powershell
npm ci   # or: npm install
npm run dev   # for development (Vite)
# or
npm run build # for production build
```

7) Run the app
```powershell
php artisan serve
# If PATH issues:
"C:\xampp\php\php.exe" artisan serve
```

## Composer Scripts
- `composer run dev` → Uses concurrently to run: php artisan serve, queue:listen, pail (logs), and `npm run dev` together.
- `composer test` → Clears config cache then runs tests.

## Useful Commands
```powershell
# DB
php artisan migrate
php artisan migrate:fresh --seed

# Cache/Config/View
php artisan optimize:clear

# Queue (if used)
php artisan queue:listen --tries=1

# Storage
php artisan storage:link

# Tests
php artisan test
```

## Troubleshooting
- "Could not open input file: artisan" → Run from project root or use full PHP path: `"C:\xampp\php\php.exe" artisan`
- "Class 'ComposerAutoloaderInit' not found" or missing vendor → Run `composer install`
- `.env` not loaded or APP_KEY empty → Run `php artisan key:generate`
- Migrations fail (SQLite) → Ensure `database/database.sqlite` file exists and `DB_CONNECTION=sqlite`
- Migrations fail (MySQL) → Create DB, correct credentials in `.env`, ensure MySQL is running
- Vite not serving assets → Run `npm run dev` in a separate terminal; check `vite.config.js` and `@vite` usage in Blade
- 403 on superadmin routes → Ensure `isSuperAdmin` middleware is implemented and assigned to the user

## Notes
- Laravel 12 requires PHP 8.2+. Verify via `php -v`. Ensure XAMPP ships a compatible PHP.
- The root route is defined twice; the later `WelcomeController@index` takes effect.