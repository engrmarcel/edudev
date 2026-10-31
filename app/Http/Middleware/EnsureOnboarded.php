<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends signed-in users who have not chosen an account type yet to the
 * onboarding screen. Staff are never onboarded.
 */
class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isOnboarded()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Onboarding is not complete.'], 409)
                : redirect()->route('onboarding');
        }

        return $next($request);
    }
}
