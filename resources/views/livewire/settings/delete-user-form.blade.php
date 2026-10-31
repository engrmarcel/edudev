<?php

use App\Services\Accounts\AccountDeletionService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

/**
 * Account deletion is a request reviewed by staff (see
 * AccountDeletionService), so retained records are handled correctly.
 */
new class extends Component {
    public string $password = '';
    public string $reason = '';

    public function requestDeletion(AccountDeletionService $deletions): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $deletions->request(Auth::user(), $this->reason ?: null);

        $this->reset('password', 'reason');
        Flux::modal('confirm-user-deletion')->close();
    }

    public function cancelRequest(AccountDeletionService $deletions): void
    {
        if ($pending = Auth::user()->deletionRequests()->pending()->first()) {
            $deletions->cancel(Auth::user(), $pending);
        }
    }

    public function with(): array
    {
        return ['pending' => Auth::user()->deletionRequests()->pending()->first()];
    }
}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Delete account') }}</flux:heading>
        <flux:subheading>{{ __('Ask us to delete your account and personal data.') }}</flux:subheading>
    </div>

    @if ($pending)
        <div role="status" class="rounded-xl border border-warning/30 bg-warning-soft p-4 text-sm text-warning">
            <p class="font-semibold">{{ __('Deletion requested on :date', ['date' => $pending->created_at->timezone(config('edudev.locale.display_timezone'))->format('j M Y')]) }}</p>
            <p class="mt-1">{{ __('Our team will review it and email you when it is done. You can keep using your account until then.') }}</p>
            <flux:button size="sm" variant="ghost" class="mt-3" wire:click="cancelRequest">{{ __('Cancel request') }}</flux:button>
        </div>
    @else
        <flux:modal.trigger name="confirm-user-deletion">
            <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
                {{ __('Request account deletion') }}
            </flux:button>
        </flux:modal.trigger>
    @endif

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="requestDeletion" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Request account deletion?') }}</flux:heading>
                <flux:subheading>
                    {{ __('Once approved, your account, learning history and personal data are permanently deleted. Some records, such as payment receipts, may be kept where the law requires. Enter your password to confirm.') }}
                </flux:subheading>
            </div>

            <flux:textarea wire:model="reason" :label="__('Reason (optional)')" rows="2" />
            <flux:input wire:model="password" id="password" :label="__('Password')" type="password" name="password" />

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Request deletion') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
