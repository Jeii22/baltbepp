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

Route::get('/', function () {
    return view('welcome');
});

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Customer Dashboard
Route::get('/customer/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('customer.dashboard');

// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'isAdmin'])->name('admin.dashboard');

// Superadmin Dashboard
Route::get('/superadmin/dashboard', function () {
    return view('superadmin.dashboard');
})->middleware(['auth', 'isSuperAdmin'])->name('superadmin.dashboard');

Route::middleware(['auth', 'isSuperAdmin'])->group(function () {
    // User management (admins)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Trips
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');

    // Fares
    Route::get('/fares', [FareController::class, 'index'])->name('fares.index');
    Route::get('/fares/create', [FareController::class, 'create'])->name('fares.create');
    Route::post('/fares', [FareController::class, 'store'])->name('fares.store');
    Route::get('/fares/{fare}/edit', [FareController::class, 'edit'])->name('fares.edit');
    Route::put('/fares/{fare}', [FareController::class, 'update'])->name('fares.update');
    Route::delete('/fares/{fare}', [FareController::class, 'destroy'])->name('fares.destroy');

    // Settings (SuperAdmin only)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
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

Route::middleware(['auth'])->group(function () {
    Route::resource('trips', TripController::class);
});

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

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


