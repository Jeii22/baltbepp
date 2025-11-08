
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    $fares = \App\Models\Fare::where('active', true)->get();
    return view('welcome', compact('fares'));
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

// Public search and booking

Route::get('/trips/search', [TripController::class, 'search'])->name('trips.search');
Route::get('/booking/schedule', [TripController::class, 'search'])->name('booking.schedule');
Route::get('/booking/schedule/passenger', [BookingController::class, 'passenger'])->name('booking.passenger');
Route::get('/booking/available-dates', [TripController::class, 'availableDates'])->name('booking.availableDates');
Route::get('/trips/{trip}/book', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings/summary', [BookingController::class, 'summary'])->name('bookings.summary');
Route::match(['GET','POST'], '/bookings/checkout', [BookingController::class, 'checkout'])->name('bookings.checkout');
Route::post('/bookings/process', [BookingController::class, 'process'])->name('bookings.process');
Route::post('/bookings/process-digital-wallet', [BookingController::class, 'processDigitalWallet'])->name('bookings.process.digital-wallet');

// PayMongo GCash routes
Route::get('/payments/paymongo/gcash/success/{booking}', [BookingController::class, 'paymongoSuccess'])->name('payments.paymongo.gcash.success');
Route::get('/payments/paymongo/gcash/failed/{booking}', [BookingController::class, 'paymongoFailed'])->name('payments.paymongo.gcash.failed');
Route::post('/webhooks/paymongo', [BookingController::class, 'paymongoWebhook'])->name('webhooks.paymongo');
Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
Route::get('/booking-status/{booking}', [BookingController::class, 'status'])->name('booking.status');

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isSuperAdmin()) {
        return view('dashboard');
    } elseif ($user->isAdmin()) {
        return view('dashboard');
    } else {
        // Regular users can also access dashboard or redirect to home
        return redirect()->route('welcome');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Google OAuth for users only
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Two-Factor Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorController::class, 'show'])->name('two-factor.login');
    Route::post('/two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('/two-factor-challenge/resend', [\App\Http\Controllers\Auth\TwoFactorController::class, 'resend'])->name('two-factor.resend');
});

// Two-Factor Management Routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::post('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/user/two-factor-authentication/confirm', [\App\Http\Controllers\Auth\TwoFactorController::class, 'confirmEnable'])->name('two-factor.confirm');
    Route::delete('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('two-factor.disable');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/user-edit', [ProfileController::class, 'userEdit'])->name('profile.user-edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Customer Dashboard with SweetAlert
Route::get('/customer/dashboard', function () {
    return view('user-dashboard', ['user' => auth()->user(), 'showAlert' => session('status') === 'profile-updated']);
})->middleware(['auth', 'verified'])->name('customer.dashboard');

// Admin Dashboard (RBAC: admin or superadmin)
Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin,super_admin'])
    ->name('admin.dashboard');

// Superadmin Dashboard (RBAC: superadmin only)
Route::get('/superadmin/dashboard', function () {
    return view('superadmin.dashboard');
})->middleware(['auth', 'role:super_admin'])->name('superadmin.dashboard');

// Superadmin-only group (RBAC)
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    // Security Overview (Super Admin Only)
    Route::get('/admin/security/overview', [\App\Http\Controllers\AdminDashboardController::class, 'securityOverview'])
        ->name('admin.security.overview');
    // Unlock User Account
    Route::post('/admin/users/{user}/unlock', function ($userId) {
        $user = \App\Models\User::findOrFail($userId);
        $user->unlockAccount();
        return back()->with('success', 'Account unlocked successfully.');
    })->name('admin.users.unlock');
    // User management (admins)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/logs', [UserController::class, 'logs'])->name('admin.users.logs');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Trips Management
    Route::resource('trips', TripController::class);

    // Fares Management
    Route::get('/fares', [FareController::class, 'index'])->name('fares.index');
    Route::get('/fares/create', [FareController::class, 'create'])->name('fares.create');
    Route::post('/fares', [FareController::class, 'store'])->name('fares.store');
    Route::get('/fares/{fare}/edit', [FareController::class, 'edit'])->name('fares.edit');
    Route::put('/fares/{fare}', [FareController::class, 'update'])->name('fares.update');
    Route::delete('/fares/{fare}', [FareController::class, 'destroy'])->name('fares.destroy');

    // Settings (SuperAdmin only)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    
        // Superadmin-only: Download DB backup
        Route::middleware(['auth', 'role:super_admin'])->group(function () {
            Route::get('/settings/backup', [DatabaseBackupController::class, 'download'])->name('settings.backup.download');
        });
});

// Shared Admin Routes (accessible by both SuperAdmin and Admin)
Route::middleware(['auth', 'hasAdminPrivileges'])->group(function () {
    // Booking Management (both SuperAdmin and Admin)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    
    // Payment Management (both SuperAdmin and Admin)
    Route::get('/admin/payment-methods', [\App\Http\Controllers\PaymentMethodController::class, 'index'])->name('admin.payment-methods.index');
    Route::get('/admin/payment-methods/create', [\App\Http\Controllers\PaymentMethodController::class, 'create'])->name('admin.payment-methods.create');
    Route::post('/admin/payment-methods', [\App\Http\Controllers\PaymentMethodController::class, 'store'])->name('admin.payment-methods.store');
    Route::get('/admin/payment-methods/{paymentMethod}/edit', [\App\Http\Controllers\PaymentMethodController::class, 'edit'])->name('admin.payment-methods.edit');
    Route::put('/admin/payment-methods/{paymentMethod}', [\App\Http\Controllers\PaymentMethodController::class, 'update'])->name('admin.payment-methods.update');
    Route::delete('/admin/payment-methods/{paymentMethod}', [\App\Http\Controllers\PaymentMethodController::class, 'destroy'])->name('admin.payment-methods.destroy');
    
    // Reports (both SuperAdmin and Admin)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Welcome Page with SweetAlert
Route::get('/welcome', function () {
    $fares = \App\Models\Fare::where('active', true)->get();
    return view('welcome', compact('fares'));
})->name('welcome');

// Route for My Bookings
Route::get('/my-bookings', function () {
    $bookings = \App\Models\Booking::with(['passengers','trip'])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return view('my-bookings', ['bookings' => $bookings]);
})->middleware(['auth', 'verified'])->name('my-bookings');

// Test routes for role-based access (for demonstration)
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin-test', function () {
        return view('test.admin-access');
    })->name('admin.test');
});

Route::middleware(['auth', 'isUser'])->group(function () {
    Route::get('/customer-test', function () {
        return view('test.customer-access');
    })->name('customer.test');
});

require __DIR__.'/auth.php';


