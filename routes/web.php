<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('resend.verification');

    Route::get('/auth/github/login', [SocialAuthController::class, 'redirectToGithub'])->name('auth.github.login');

    Route::get('/password/reset', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::get('/auth/github/callback', [SocialAuthController::class, 'handleGithubCallback']);

Route::get('/two-factor/verify', [TwoFactorController::class, 'showVerify'])->name('two-factor.verify');
Route::post('/two-factor/verify', [TwoFactorController::class, 'verify']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::get('/two-factor/setup', [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    Route::get('/auth/github', [SocialAuthController::class, 'redirectToGithub'])->name('auth.github');
    Route::post('/auth/github/unlink', [SocialAuthController::class, 'unlinkGithub'])->name('auth.github.unlink');

    Route::get('/admin/login-attempts', [AdminController::class, 'loginAttempts'])->name('admin.login-attempts');
    Route::get('/admin/user-stats/{email}', [AdminController::class, 'userStats'])->name('admin.user-stats');
});
