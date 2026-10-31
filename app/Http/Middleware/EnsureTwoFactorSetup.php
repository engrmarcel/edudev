<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When two-step login is switched on platform-wide, users whose role
 * requires it (staff by default) are held on the setup screen until it is on.
 */
class EnsureTwoFactorSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->requiresTwoFactor() && ! $user->hasTwoFactorEnabled()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Two-step login must be set up first.'], 403);
            }

            return redirect()->route('settings.two-factor')
                ->with('status', 'two-factor-required');
        }

        return $next($request);
    }
}
