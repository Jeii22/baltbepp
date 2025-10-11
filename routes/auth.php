<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\OtpPasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Hidden Superadmin login
    Route::get('administration-login', [\App\Http\Controllers\Auth\SuperAdminLoginController::class, 'show'])
        ->name('administration.login');
    Route::post('administration-login', [\App\Http\Controllers\Auth\SuperAdminLoginController::class, 'login'])
        ->name('administration.login.attempt');

    Route::get('forgot-password', [OtpPasswordResetController::class, 'create'])
        ->name('password.request.otp');

    Route::post('forgot-password', [OtpPasswordResetController::class, 'sendOtp'])
        ->name('password.send.otp');

    Route::get('verify-otp', [OtpPasswordResetController::class, 'showVerifyForm'])
        ->name('password.verify.form');

    Route::post('verify-otp', [OtpPasswordResetController::class, 'verifyOtp'])
        ->name('password.verify.otp');

    Route::get('reset-password-otp', [OtpPasswordResetController::class, 'showResetForm'])
        ->name('password.reset.otp');

    Route::post('reset-password-otp', [OtpPasswordResetController::class, 'resetPassword'])
        ->name('password.reset.otp.post');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
