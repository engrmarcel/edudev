<?php

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Accounts\AccountAdministration;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

/**
 * Account details for staff. Deliberately shows NO tutor conversations or
 * other private learning content.
 */
new #[Title('User details')] class extends Component {
    public User $user;
    public string $reason = '';
    public array $roles = [];
    public ?string $actionError = null;

    public function mount(User $user): void
    {
        Gate::authorize(Permission::ManageUsers->value);
        $this->user = $user;
        $this->roles = $user->roles->pluck('name')->all();
    }

    public function suspend(AccountAdministration $admin): void
    {
        $this->validate(['reason' => ['required', 'string', 'max:500']]);
        $this->run(fn () => $admin->suspend(auth()->user(), $this->user, $this->reason));
        $this->reason = '';
    }

    public function reactivate(AccountAdministration $admin): void
    {
        $this->run(fn () => $admin->reactivate(auth()->user(), $this->user));
    }

    public function resetTwoFactor(AccountAdministration $admin): void
    {
        $this->run(fn () => $admin->resetTwoFactor(auth()->user(), $this->user));
    }

    public function saveRoles(AccountAdministration $admin): void
    {
        $this->run(fn () => $admin->syncRoles(auth()->user(), $this->user, $this->roles));
        $this->roles = $this->user->fresh()->roles->pluck('name')->all();
    }

    private function run(callable $action): void
    {
        $this->actionError = null;

        try {
            $action();
            $this->user->refresh();
            $this->dispatch('user-saved');
        } catch (AuthorizationException $e) {
            $this->actionError = $e->getMessage() ?: __('You are not allowed to do that.');
        }
    }

    public function with(): array
    {
        return [
            'history' => AuditLog::query()
                ->where('subject_type', $this->user->getMorphClass())
                ->where('subject_id', $this->user->id)
                ->latest('id')->limit(20)->get(),
            'canManageRoles' => Gate::allows(Permission::ManageRoles->value),
            'isSelf' => auth()->id() === $this->user->id,
        ];
    }
}; ?>

<div class="mx-auto w-full max-w-5xl space-y-6 p-4 sm:p-6 lg:p-8">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-secondary-strong hover:underline" wire:navigate>← {{ __('All users') }}</a>

    <header class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white">{{ $user->name }}</h1>
            <p class="text-sm text-ink-soft">{{ $user->email }}</p>
        </div>
        @if ($user->isSuspended())
            <span class="inline-flex items-center gap-1 rounded-full bg-danger-soft px-3 py-1 text-sm font-medium text-danger"><flux:icon name="no-symbol" variant="mini" /> {{ __('Suspended') }}</span>
        @else
            <span class="inline-flex items-center gap-1 rounded-full bg-success-soft px-3 py-1 text-sm font-medium text-success"><flux:icon name="check-circle" variant="mini" /> {{ __('Active') }}</span>
        @endif
    </header>

    @if ($actionError)
        <div role="alert" class="rounded-xl bg-danger-soft p-4 text-sm text-danger">{{ $actionError }}</div>
    @endif
    @error('user') <div role="alert" class="rounded-xl bg-danger-soft p-4 text-sm text-danger">{{ $message }}</div> @enderror
    <x-action-message on="user-saved" class="text-sm text-success">{{ __('Saved.') }}</x-action-message>

    <section class="card">
        <h2 class="font-bold">{{ __('Account') }}</h2>
        <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
            <div><dt class="text-ink-soft">{{ __('Account type') }}</dt><dd class="font-medium">{{ $user->profile?->account_type?->label() ?? __('Not onboarded') }}</dd></div>
            <div><dt class="text-ink-soft">{{ __('Email verified') }}</dt><dd class="font-medium">{{ $user->email_verified_at ? __('Yes') : __('No') }}</dd></div>
            <div><dt class="text-ink-soft">{{ __('Joined') }}</dt><dd class="font-medium">{{ $user->created_at->timezone(config('edudev.locale.display_timezone'))->format('j M Y, H:i') }}</dd></div>
            <div><dt class="text-ink-soft">{{ __('Two-step login') }}</dt><dd class="font-medium">{{ $user->hasTwoFactorEnabled() ? __('On') : __('Off') }}</dd></div>
            <div><dt class="text-ink-soft">{{ __('Last sign-in') }}</dt><dd class="font-medium">{{ $user->last_login_at?->timezone(config('edudev.locale.display_timezone'))->format('j M Y, H:i') ?? '—' }}</dd></div>
            @if ($user->profile?->organisation_name)
                <div><dt class="text-ink-soft">{{ __('Organisation') }}</dt><dd class="font-medium">{{ $user->profile->organisation_name }}</dd></div>
            @endif
            @if ($user->isSuspended())
                <div class="sm:col-span-3"><dt class="text-ink-soft">{{ __('Suspension reason') }}</dt><dd class="font-medium">{{ $user->suspension_reason }}</dd></div>
            @endif
        </dl>
    </section>

    @unless ($isSelf)
        <section class="card space-y-4">
            <h2 class="font-bold">{{ $user->isSuspended() ? __('Reactivate account') : __('Suspend account') }}</h2>
            @if ($user->isSuspended())
                <flux:button wire:click="reactivate" variant="primary">{{ __('Reactivate') }}</flux:button>
            @else
                <p class="text-sm text-ink-soft">{{ __('The user is signed out immediately and cannot sign in until reactivated. The reason is recorded in the audit log.') }}</p>
                <form wire:submit="suspend" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <flux:input wire:model="reason" :label="__('Reason')" class="flex-1" required />
                    <flux:button type="submit" variant="danger" wire:confirm="{{ __('Suspend this account?') }}">{{ __('Suspend') }}</flux:button>
                </form>
            @endif
        </section>

        @if ($user->hasTwoFactorEnabled())
            <section class="card space-y-3">
                <h2 class="font-bold">{{ __('Reset two-step login') }}</h2>
                <p class="text-sm text-ink-soft">{{ __('Only use this when the person has lost their phone and recovery codes, after confirming who they are. They will be signed out and can set it up again.') }}</p>
                <flux:button variant="danger" wire:click="resetTwoFactor" wire:confirm="{{ __('Reset two-step login for this user?') }}">{{ __('Reset two-step login') }}</flux:button>
            </section>
        @endif

        @if ($canManageRoles)
            <section class="card space-y-4">
                <h2 class="font-bold">{{ __('Roles') }}</h2>
                <form wire:submit="saveRoles" class="space-y-4">
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach (Role::cases() as $r)
                            <flux:checkbox wire:model="roles" :value="$r->value" :label="$r->label()" />
                        @endforeach
                    </div>
                    <flux:button type="submit" variant="primary">{{ __('Save roles') }}</flux:button>
                </form>
            </section>
        @endif
    @else
        <p class="text-sm text-ink-soft">{{ __('You cannot suspend yourself or change your own roles.') }}</p>
    @endunless

    <section class="card">
        <h2 class="font-bold">{{ __('Account history') }}</h2>
        @forelse ($history as $log)
            <div class="flex justify-between gap-4 border-b border-line py-2 text-sm last:border-0 dark:border-zinc-800">
                <span><code class="text-xs font-semibold">{{ $log->action }}</code> <span class="text-ink-soft">· {{ $log->actor_label }}</span></span>
                <time class="shrink-0 text-xs text-ink-soft">{{ $log->created_at->timezone(config('edudev.locale.display_timezone'))->format('j M Y, H:i') }}</time>
            </div>
        @empty
            <p class="mt-3 text-sm text-ink-soft">{{ __('No recorded changes.') }}</p>
        @endforelse
    </section>
</div>
