<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Session;

/**
 * Holds a sign-in that passed the password check but still needs the
 * two-step code. The user is NOT authenticated while this is pending.
 */
class PendingTwoFactorLogin
{
    private const KEY = 'two_factor.pending';

    /** @param  'user'|'admin'  $portal */
    public static function start(User $user, bool $remember, string $portal): void
    {
        Session::regenerate();
        Session::put(self::KEY, [
            'id' => $user->getKey(),
            'remember' => $remember,
            'portal' => $portal,
            'expires' => now()->addMinutes((int) config('edudev.two_factor.challenge_ttl_minutes', 10))->getTimestamp(),
        ]);
    }

    /** @return array{user: User, remember: bool, portal: string}|null */
    public static function current(): ?array
    {
        $data = Session::get(self::KEY);

        if (! is_array($data) || ($data['expires'] ?? 0) < now()->getTimestamp()) {
            self::clear();

            return null;
        }

        $user = User::query()->find($data['id']);
        if (! $user || $user->isSuspended() || ! $user->hasTwoFactorEnabled()) {
            self::clear();

            return null;
        }

        return ['user' => $user, 'remember' => (bool) $data['remember'], 'portal' => $data['portal']];
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }
}
