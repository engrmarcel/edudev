<?php

use App\Enums\Permission;
use App\Models\AccountDeletionRequest;
use App\Services\Accounts\AccountDeletionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Deletion requests')] class extends Component {
    use WithPagination;

    public array $notes = [];
    public ?string $actionError = null;

    public function mount(): void
    {
        Gate::authorize(Permission::ManageUsers->value);
    }

    public function complete(int $id, AccountDeletionService $service): void
    {
        $this->act($id, fn ($r) => $service->complete(auth()->user(), $r, $this->notes[$id] ?? null));
    }

    public function reject(int $id, AccountDeletionService $service): void
    {
        $this->act($id, fn ($r) => $service->reject(auth()->user(), $r, (string) ($this->notes[$id] ?? '')));
    }

    private function act(int $id, callable $fn): void
    {
        Gate::authorize(Permission::ManageUsers->value);
        $this->actionError = null;

        try {
            $fn(AccountDeletionRequest::query()->findOrFail($id));
            unset($this->notes[$id]);
        } catch (AuthorizationException $e) {
            $this->actionError = $e->getMessage() ?: __('You are not allowed to do that.');
        }
    }

    public function with(): array
    {
        return [
            'pending' => AccountDeletionRequest::query()->pending()->oldest()->get(),
            'recent' => AccountDeletionRequest::query()->where('status', '!=', AccountDeletionRequest::PENDING)->with('reviewer:id,name')->latest('updated_at')->paginate(15),
        ];
    }
}; ?>

<div class="mx-auto w-full max-w-5xl space-y-6 p-4 sm:p-6 lg:p-8">
    <header>
        <p class="eyebrow">{{ __('Administration') }}</p>
        <h1 class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ __('Account deletion requests') }}</h1>
        <p class="mt-1 text-sm text-ink-soft">{{ __('Completing a request permanently deletes the account and its personal data. Check for records that must be kept (for example payments) first.') }}</p>
    </header>

    @if ($actionError)
        <div role="alert" class="rounded-xl bg-danger-soft p-4 text-sm text-danger">{{ $actionError }}</div>
    @endif
    @foreach (['request', 'note'] as $field)
        @error($field) <div role="alert" class="rounded-xl bg-danger-soft p-4 text-sm text-danger">{{ $message }}</div> @enderror
    @endforeach

    <section class="space-y-3" aria-labelledby="pending-h">
        <h2 id="pending-h" class="font-bold">{{ __('Waiting for review') }} ({{ $pending->count() }})</h2>
        @forelse ($pending as $r)
            <div class="card space-y-3" wire:key="dr-{{ $r->id }}">
                <div class="flex flex-wrap justify-between gap-2">
                    <div>
                        <p class="font-semibold">{{ $r->user_label }}</p>
                        <p class="text-xs text-ink-soft">{{ __('Requested :date', ['date' => $r->created_at->timezone(config('edudev.locale.display_timezone'))->format('j M Y, H:i')]) }}</p>
                    </div>
                    @if ($r->user_id)
                        <a href="{{ route('admin.users.show', $r->user_id) }}" class="text-sm text-secondary-strong hover:underline" wire:navigate>{{ __('View account') }}</a>
                    @endif
                </div>
                @if ($r->reason)
                    <p class="rounded-lg bg-surface-alt p-3 text-sm dark:bg-zinc-800">{{ $r->reason }}</p>
                @endif
                <flux:input wire:model="notes.{{ $r->id }}" :label="__('Note (required to reject)')" />
                <div class="flex gap-2">
                    <flux:button size="sm" variant="danger" wire:click="complete({{ $r->id }})" wire:confirm="{{ __('Permanently delete this account?') }}">{{ __('Delete account') }}</flux:button>
                    <flux:button size="sm" variant="ghost" wire:click="reject({{ $r->id }})">{{ __('Reject') }}</flux:button>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink-soft">{{ __('No requests waiting.') }}</p>
        @endforelse
    </section>

    <section class="card overflow-x-auto p-0" aria-labelledby="recent-h">
        <h2 id="recent-h" class="px-4 pt-4 font-bold">{{ __('Handled') }}</h2>
        <table class="mt-2 w-full min-w-[560px] text-left text-sm">
            <thead class="border-y border-line bg-surface-alt text-xs text-ink-soft uppercase dark:border-zinc-800 dark:bg-zinc-800">
                <tr><th class="px-4 py-2">{{ __('Account') }}</th><th class="px-4 py-2">{{ __('Outcome') }}</th><th class="px-4 py-2">{{ __('By') }}</th><th class="px-4 py-2">{{ __('When') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-line dark:divide-zinc-800">
                @forelse ($recent as $r)
                    <tr><td class="px-4 py-2">{{ $r->user_label }}</td><td class="px-4 py-2 capitalize">{{ $r->status }}</td><td class="px-4 py-2">{{ $r->reviewer?->name ?? ($r->status === 'cancelled' ? __('User') : '—') }}</td><td class="px-4 py-2 text-ink-soft">{{ $r->updated_at->timezone(config('edudev.locale.display_timezone'))->format('j M Y') }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-ink-soft">{{ __('Nothing yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
    {{ $recent->links() }}
</div>
