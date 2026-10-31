<?php

use App\Services\Auth\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Two-step login')] class extends Component {
    public string $code = '';
    public string $password = '';
    /** Recovery codes, shown ONCE right after they are generated. */
    public array $freshCodes = [];

    public function mount(): void
    {
        abort_unless(TwoFactorService::featureEnabled(), 404);
    }

    public function start(TwoFactorService $twoFactor): void
    {
        $this->validate(['password' => ['required', 'current_password']]);
        $twoFactor->begin(Auth::user());
        $this->reset('password', 'code');
    }

    public function confirm(TwoFactorService $twoFactor): void
    {
        $this->validate(['code' => ['required', 'digits:6']]);

        $codes = $twoFactor->confirm(Auth::user(), $this->code);
        if ($codes === null) {
            $this->addError('code', __('That code is not valid. Check the time on your phone and try again.'));

            return;
        }

        $this->freshCodes = $codes;
        $this->reset('code');
    }

    public function cancelSetup(TwoFactorService $twoFactor): void
    {
        if (! Auth::user()->hasTwoFactorEnabled()) {
            $twoFactor->disable(Auth::user());
        }
    }

    public function regenerate(TwoFactorService $twoFactor): void
    {
        $this->validate(['password' => ['required', 'current_password']]);
        $this->freshCodes = $twoFactor->regenerateRecoveryCodes(Auth::user());
        $this->reset('password');
    }

    public function disable(TwoFactorService $twoFactor): void
    {
        if (Auth::user()->requiresTwoFactor()) {
            $this->addError('password', __('Two-step login is required for your account and cannot be turned off.'));

            return;
        }

        $this->validate(['password' => ['required', 'current_password']]);
        $twoFactor->disable(Auth::user());
        $this->reset('password', 'freshCodes');
    }

    public function with(TwoFactorService $twoFactor): array
    {
        $user = Auth::user()->fresh();
        $settingUp = $user->two_factor_secret !== null && ! $user->hasTwoFactorEnabled();

        return [
            'enabled' => $user->hasTwoFactorEnabled(),
            'settingUp' => $settingUp,
            'required' => $user->requiresTwoFactor(),
            'qr' => $settingUp ? $twoFactor->qrCodeSvg($user) : null,
            'secret' => $settingUp ? $twoFactor->displaySecret($user) : null,
            'remaining' => count($user->two_factor_recovery_codes ?? []),
        ];
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Two-step login')" :subheading="__('Protect your account with a code from an authenticator app as well as your password.')">
        <div class="my-6 w-full space-y-6">
            @if (session('status') === 'two-factor-required' || ($required && ! $enabled))
                <div role="alert" class="rounded-xl border border-warning/30 bg-warning-soft p-4 text-sm text-warning">
                    <strong>{{ __('Required for your account.') }}</strong>
                    {{ __('Set up two-step login to continue using :product.', ['product' => $brand->get('product_name')]) }}
                </div>
            @endif

            @if ($freshCodes)
                <div class="rounded-xl border border-line bg-surface-alt p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="font-semibold">{{ __('Save these recovery codes') }}</p>
                    <p class="mt-1 text-sm text-ink-soft dark:text-zinc-400">{{ __('Each one works once if you lose your phone. They will not be shown again. Store them somewhere safe, such as a password manager or on paper.') }}</p>
                    <ul class="mt-3 grid grid-cols-2 gap-2 font-mono text-sm">
                        @foreach ($freshCodes as $c)
                            <li class="rounded bg-surface px-2 py-1 dark:bg-zinc-900">{{ $c }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($enabled)
                <p class="inline-flex items-center gap-2 rounded-lg bg-success-soft px-3 py-2 text-sm font-medium text-success">
                    <flux:icon name="shield-check" variant="mini" /> {{ __('Two-step login is on.') }}
                    <span class="font-normal">{{ trans_choice(':count recovery code left|:count recovery codes left', $remaining) }}</span>
                </p>

                <form wire:submit="regenerate" class="space-y-3">
                    <flux:input wire:model="password" type="password" :label="__('Confirm your password')" autocomplete="current-password" />
                    <div class="flex flex-wrap gap-2">
                        <flux:button type="submit">{{ __('New recovery codes') }}</flux:button>
                        @unless ($required)
                            <flux:button type="button" variant="danger" wire:click="disable" wire:confirm="{{ __('Turn off two-step login?') }}">{{ __('Turn off') }}</flux:button>
                        @endunless
                    </div>
                </form>
            @elseif ($settingUp)
                <ol class="list-decimal space-y-4 pl-5 text-sm">
                    <li>{{ __('Install an authenticator app, such as Google Authenticator or Microsoft Authenticator.') }}</li>
                    <li>
                        {{ __('Scan this QR code with the app:') }}
                        <div class="mt-3 inline-block rounded-xl bg-white p-3 ring-1 ring-line" role="img" aria-label="{{ __('QR code for two-step login setup') }}">{!! $qr !!}</div>
                        <p class="mt-2 text-ink-soft">{{ __('Or type this key:') }} <code class="font-mono font-semibold tracking-wider text-ink dark:text-white">{{ $secret }}</code></p>
                    </li>
                    <li>{{ __('Enter the 6-digit code the app shows.') }}</li>
                </ol>
                <form wire:submit="confirm" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <flux:input wire:model="code" :label="__('6-digit code')" inputmode="numeric" maxlength="6" autocomplete="one-time-code" class="sm:w-48" />
                    <flux:button type="submit" variant="primary">{{ __('Turn on') }}</flux:button>
                    <flux:button type="button" variant="ghost" wire:click="cancelSetup">{{ __('Cancel') }}</flux:button>
                </form>
            @else
                <p class="text-sm text-ink-soft dark:text-zinc-400">{{ __('When this is on, signing in needs your password and a code from your phone, so a stolen password alone is not enough.') }}</p>
                <form wire:submit="start" class="space-y-3">
                    <flux:input wire:model="password" type="password" :label="__('Confirm your password')" autocomplete="current-password" />
                    <flux:button type="submit" variant="primary">{{ __('Set up two-step login') }}</flux:button>
                </form>
            @endif
        </div>
    </x-settings.layout>
</section>
