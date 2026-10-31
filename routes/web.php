<?php

use App\Enums\Permission;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\System\HealthController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('home');

Route::get('legal/{page}', LegalPageController::class)
    ->where('page', implode('|', array_keys(LegalPageController::PAGES)))
    ->name('legal.show');

Route::get('health', HealthController::class)
    ->middleware('throttle:30,1')
    ->name('health');

/*
|--------------------------------------------------------------------------
| Signed-in users (students and other account types)
|--------------------------------------------------------------------------
*/
// Second step of sign-in (only reachable mid-login, see PendingTwoFactorLogin).
Volt::route('two-factor-challenge', 'auth.two-factor-challenge')
    ->middleware(['guest', 'throttle:20,1'])
    ->name('two-factor.challenge');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'two-factor.setup', 'onboarded'])
    ->name('dashboard');

Volt::route('welcome/setup', 'onboarding')
    ->middleware(['auth', 'verified'])
    ->name('onboarding');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('settings/two-factor', 'settings.two-factor')->name('settings.two-factor');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Staff / admin area
|--------------------------------------------------------------------------
| Separate sign-in page from /login. Path is configurable (ADMIN_PATH).
*/
Route::prefix(config('edudev.admin.path'))->name('admin.')->group(function () {
    Volt::route('login', 'admin.auth.login')
        ->middleware('guest')
        ->name('login');

    Route::middleware(['auth', 'verified', 'permission:'.Permission::AccessAdmin->value, 'two-factor.setup'])->group(function () {
        Volt::route('/', 'admin.dashboard')->name('dashboard');

        Volt::route('branding', 'admin.branding')
            ->middleware('permission:'.Permission::ManageBranding->value)
            ->name('branding');

        Volt::route('audit-log', 'admin.audit-log')
            ->middleware('permission:'.Permission::ViewAuditLogs->value)
            ->name('audit');

        Route::middleware('permission:'.Permission::ManageUsers->value)->group(function () {
            Volt::route('users', 'admin.users.index')->name('users.index');
            Volt::route('users/{user}', 'admin.users.show')->name('users.show');
            Volt::route('deletion-requests', 'admin.deletion-requests')->name('deletion-requests');
        });
    });
});
