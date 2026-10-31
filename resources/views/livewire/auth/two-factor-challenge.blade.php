<?php

use App\Services\Audit\AuditLogger;
use App\Services\Auth\PendingTwoFactorLogin;
use App\Services\Auth\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] #[Title('Two-step login')] class extends Component {
    public string $code = '';
    public bool $useRecovery = false;

    public function mount(): void
    {
        if (! TwoFactorService::featureEnabled() || ! PendingTwoFactorLogin::current()) {
            $this->redirectRoute('login', navigate: true);
        }
    }

    public function verify(TwoFactorService $twoFactor, AuditLogger $audit): void
    {
        $pending = PendingTwoFactorLogin::current();
        if (! $pending) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->validate(['code' => ['required', 'string', 'max:32']]);

        $user = $pending['user'];
        $key = 'two-factor|'.$user->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'code' => __('Too many attempts. Try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        if (! $twoFactor->verifyLogin($user, $this->code)) {
            RateLimiter::hit($key, 300);
            $this->code = '';

            throw ValidationException::withMessages(['code' => __('That code is not valid. Check your app and try again.')]);
        }

        RateLimiter::clear($key);
        PendingTwoFactorLogin::clear();

        Auth::login($user, $pending['remember']);
        Session::regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        if ($pending['portal'] === 'admin') {
            $audit->record('admin.login', $user, context: ['two_factor' => true], actor: $user);
        }

        $this->redirectIntended(default: $user->homeUrl(), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Two-step login')"
        :description="$useRecovery
            ? __('Enter one of the recovery codes you saved when you set up two-step login.')
            : __('Open your authenticator app and enter the 6-digit code for :product.', ['product' => $brand->get('product_name')])" />

    <form wire:submit="verify" class="flex flex-col gap-6">
        @if ($useRecovery)
            <flux:input wire:model="code" :label="__('Recovery code')" autocomplete="one-time-code" autofocus required />
        @else
            <flux:input wire:model="code" :label="__('6-digit code')" inputmode="numeric" pattern="[0-9 ]*" maxlength="7" autocomplete="one-time-code" autofocus required />
        @endif

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Verify and sign in') }}</flux:button>
    </form>

    <button type="button" wire:click="$toggle('useRecovery')" class="text-center text-sm font-medium text-secondary-strong hover:underline">
        {{ $useRecovery ? __('Use a code from my app instead') : __('Lost your phone? Use a recovery code') }}
    </button>
</div>
