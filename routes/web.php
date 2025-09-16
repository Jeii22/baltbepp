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
Route::post('/bookings/checkout', [BookingController::class, 'checkout'])->name('bookings.checkout');
Route::post('/bookings/process', [BookingController::class, 'process'])->name('bookings.process');
Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Google OAuth for users only
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/superadmin/dashboard', function () {
    return view('superadmin.dashboard');
})->name('superadmin.dashboard');

Route::middleware(['auth', 'isSuperAdmin'])->group(function () {
    // User management (admins)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
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

    // Other
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
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


