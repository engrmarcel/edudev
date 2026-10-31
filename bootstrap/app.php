<?php

use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureOnboarded;
use App\Http\Middleware\EnsureTwoFactorSetup;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(AssignRequestId::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->appendToGroup('web', EnsureAccountIsActive::class);

        // Signed-out visitors to the admin area go to the STAFF sign-in page;
        // everyone else goes to the normal user sign-in page.
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is(config('edudev.admin.path'), config('edudev.admin.path').'/*')
            ? route('admin.login')
            : route('login'));

        // Signed-in users who open a sign-in page are sent to their own area.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->homeUrl() ?? route('dashboard'));

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'onboarded' => EnsureOnboarded::class,
            'two-factor.setup' => EnsureTwoFactorSetup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Never write credentials or tokens into logs.
        $exceptions->dontFlash(['current_password', 'password', 'password_confirmation', 'token', 'api_key', 'secret']);
    })->create();
