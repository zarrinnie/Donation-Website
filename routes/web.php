<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
// Auth Routes
use App\Livewire\Admin\Donation\Index as AdminDonationIndex;
use App\Livewire\Admin\DonationSetting\Index as AdminDonationSettingIndex;
use App\Livewire\Admin\Donor\Index as AdminDonorIndex;
use App\Livewire\Admin\GlobalSearch;
// Email Verification Routes
use App\Livewire\Admin\User\Index as AdminUserIndex;
// Route khusus untuk handle klik link dari email (Laravel Handle Otomatis)
use App\Livewire\Auth\ForgotPassword;
// Public Routes
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Public\About;
// Admin Routes
use App\Livewire\Public\Donate;
use App\Livewire\Public\Landing;
use App\Livewire\Public\Payment;
use App\Livewire\Public\ThankYou;
use App\Livewire\User\Dashboard as UserDashboard;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
// User Routes
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ============
Route::get('/', Landing::class)->name('home');
Route::get('/about', About::class)->name('about');
Route::get('/donate', Donate::class)->name('donate');
Route::get('/donate/payment', Payment::class)->name('donate.payment');
Route::get('/donate/thank-you', ThankYou::class)->name('donate.thank-you');

// Route khusus untuk halaman "Please Verify"
Route::get('/email/verify', VerifyEmail::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('user.dashboard'); // Redirect kemana setelah sukses
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::middleware(['auth'])->group(function () {
    // Generic dashboard entrypoint (Fortify's configured "home") — routes the
    // signed-in user to the correct role dashboard.
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return redirect()->route($user && $user->isAdmin() ? 'admin.dashboard' : 'user.dashboard');
    })->name('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');

    // Halaman Request Link
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');

    // Halaman Input Password Baru (Link dari Email akan mengarah kesini)
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::get('/register', Register::class)->name('register')->middleware('guest');

// ============ ADMIN (super_admin + admin) ============
Route::middleware(['auth', 'role:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/global-search', GlobalSearch::class)->name('global-search');

    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');              // Latest Donations overview
    Route::get('/donations', AdminDonationIndex::class)->name('donations');          // Detailed ledger + status
    Route::get('/donation-settings', AdminDonationSettingIndex::class)->name('donation-settings');
    Route::get('/donors', AdminDonorIndex::class)->name('donors');                   // Unique donor directory
});

// ============ ADMIN (super_admin only) ============
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', AdminUserIndex::class)->name('users');                      // Manage admin accounts
});

Route::middleware(['auth', 'verified', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
});
