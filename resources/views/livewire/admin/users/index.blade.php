<?php

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Users')] class extends Component {
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $role = '';
    #[Url] public string $status = '';

    public function mount(): void
    {
        Gate::authorize(Permission::ManageUsers->value);
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'role', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function with(): array
    {
        Gate::authorize(Permission::ManageUsers->value);

        $term = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], trim($this->search));

        $users = User::query()
            ->with(['roles:id,name', 'profile:id,user_id,account_type'])
            ->when($term !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")))
            ->when(Role::tryFrom($this->role), fn ($q, $r) => $q->role($r->value))
            ->when(in_array($this->status, [User::STATUS_ACTIVE, User::STATUS_SUSPENDED], true), fn ($q) => $q->where('status', $this->status))
            ->latest('id')
            ->paginate(25);

        return ['users' => $users];
    }
}; ?>

<div class="mx-auto w-full max-w-6xl space-y-6 p-4 sm:p-6 lg:p-8">
    <header>
        <p class="eyebrow">{{ __('Administration') }}</p>
        <h1 class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ __('Users') }}</h1>
    </header>

    <div class="grid gap-3 sm:grid-cols-3">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search name or email')" icon="magnifying-glass" />
        <flux:select wire:model.live="role" :label="__('Role')">
            <flux:select.option value="">{{ __('All roles') }}</flux:select.option>
            @foreach (Role::cases() as $r)
                <flux:select.option :value="$r->value">{{ $r->label() }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="status" :label="__('Status')">
            <flux:select.option value="">{{ __('Any status') }}</flux:select.option>
            <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
            <flux:select.option value="suspended">{{ __('Suspended') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="card overflow-x-auto p-0">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="border-b border-line bg-surface-alt text-xs tracking-wide text-ink-soft uppercase dark:border-zinc-800 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-4 py-3">{{ __('Name') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Roles') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Status') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Joined') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Last sign-in') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line dark:divide-zinc-800">
                @forelse ($users as $u)
                    <tr wire:key="u-{{ $u->id }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $u) }}" class="font-semibold text-primary hover:underline dark:text-white" wire:navigate>{{ $u->name }}</a>
                            <div class="text-xs text-ink-soft">{{ $u->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @forelse ($u->roles as $r)
                                <span class="mr-1 inline-block rounded-full bg-surface-alt px-2 py-0.5 text-xs dark:bg-zinc-800">{{ Role::tryFrom($r->name)?->label() ?? $r->name }}</span>
                            @empty
                                <span class="text-xs text-muted">{{ __('Not onboarded') }}</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3">
                            @if ($u->isSuspended())
                                <span class="inline-flex items-center gap-1 rounded-full bg-danger-soft px-2 py-0.5 text-xs font-medium text-danger"><flux:icon name="no-symbol" variant="micro" /> {{ __('Suspended') }}</span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-success-soft px-2 py-0.5 text-xs font-medium text-success"><flux:icon name="check-circle" variant="micro" /> {{ __('Active') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-ink-soft">{{ $u->created_at->timezone(config('edudev.locale.display_timezone'))->format('j M Y') }}</td>
                        <td class="px-4 py-3 text-ink-soft">{{ $u->last_login_at?->timezone(config('edudev.locale.display_timezone'))->diffForHumans() ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-ink-soft">{{ __('No users match these filters.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
