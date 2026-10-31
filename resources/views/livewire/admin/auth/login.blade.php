<?php

use App\Enums\Permission;
use App\Services\Audit\AuditLogger;
use App\Services\Auth\PendingTwoFactorLogin;
use App\Services\Auth\TwoFactorService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

/**
 * Staff sign-in. Separate from the student/user /login page.
 *
 * - Only accounts with the admin.access permission can sign in here.
 * - Non-staff credentials get the same generic error as a wrong password,
 *   so this page cannot be used to discover which emails are staff.
 * - Stricter rate limit than the user page, no "remember me".
 */
new #[Layout('components.layouts.admin-auth')] #[Title('Staff sign-in')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        // Check the password WITHOUT signing in yet.
        $provider = Auth::guard('web')->getProvider();
        $user = $provider->retrieveByCredentials(['email' => $this->email]);

        if (! $user || ! $provider->validateCredentials($user, ['password' => $this->password])) {
            $this->fail('invalid_credentials');
        }

        if ($user->isSuspended() || ! $user->can(Permission::AccessAdmin->value)) {
            $this->fail($user->isSuspended() ? 'suspended' : 'not_staff');
        }

        RateLimiter::clear($this->throttleKey());

        if (TwoFactorService::featureEnabled() && $user->hasTwoFactorEnabled()) {
            PendingTwoFactorLogin::start($user, false, 'admin');
            $this->redirectRoute('two-factor.challenge', navigate: true);

            return;
        }

        // Staff who must use two-step login but haven't set it up yet are
        // signed in, then held on the setup screen (EnsureTwoFactorSetup).
        Auth::login($user);
        Session::regenerate();

        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        app(AuditLogger::class)->record('admin.login', $user, actor: $user);

        $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
    }

    private function fail(string $reason): never
    {
        RateLimiter::hit($this->throttleKey(), 900);

        Log::warning('Admin sign-in rejected', [
            'reason' => $reason,
            'email_hash' => hash('sha256', Str::lower($this->email)),
            'ip' => request()->ip(),
        ]);

        throw ValidationException::withMessages(['email' => __('auth.failed')]);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    protected function throttleKey(): string
    {
        return 'admin-login|'.Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="text-center">
        <p class="text-xs font-semibold tracking-widest text-gold uppercase">{{ __('Staff portal') }}</p>
        <h1 class="mt-2 text-2xl font-bold text-ink">{{ __('Staff sign-in') }}</h1>
        <p class="mt-1 text-sm text-ink-soft">{{ __('For :product administrators and staff only.', ['product' => $brand->get('product_name')]) }}</p>
    </div>

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input wire:model="email" :label="__('Work email address')" type="email" name="email" required autofocus autocomplete="username" />

        <flux:input wire:model="password" :label="__('Password')" type="password" name="password" required autocomplete="current-password" />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Sign in to staff portal') }}</flux:button>
    </form>

    <p class="text-center text-xs text-ink-soft">
        {{ __('Not staff?') }}
        <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">{{ __('Go to the student and user sign-in') }}</a>
    </p>
</div>
