<?php

use App\Enums\Permission;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Audit log')] class extends Component {
    use WithPagination;

    #[Url]
    public string $action = '';

    public function mount(): void
    {
        Gate::authorize(Permission::ViewAuditLogs->value);
    }

    public function updatedAction(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        Gate::authorize(Permission::ViewAuditLogs->value);

        return [
            'logs' => AuditLog::query()
                ->when($this->action !== '', fn ($q) => $q->where('action', 'like', str_replace(['%', '_'], ['\%', '\_'], $this->action).'%'))
                ->latest('id')
                ->paginate(25),
        ];
    }
}; ?>

<div class="mx-auto w-full max-w-6xl space-y-6 p-4 sm:p-6 lg:p-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="eyebrow">{{ __('Administration') }}</p>
            <h1 class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ __('Audit log') }}</h1>
            <p class="mt-1 text-sm text-ink-soft dark:text-zinc-400">{{ __('Append-only record of sensitive actions. Times shown in :tz.', ['tz' => config('edudev.locale.display_timezone')]) }}</p>
        </div>
        <flux:input wire:model.live.debounce.300ms="action" :label="__('Filter by action')" placeholder="branding." class="sm:w-64" />
    </header>

    <div class="card overflow-x-auto p-0">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-surface-alt text-xs tracking-wide text-ink-soft uppercase dark:border-zinc-800 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-4 py-3">{{ __('When') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Action') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('By') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Details') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line dark:divide-zinc-800">
                @forelse ($logs as $log)
                    <tr class="align-top">
                        <td class="px-4 py-3 whitespace-nowrap text-ink-soft">{{ $log->created_at->timezone(config('edudev.locale.display_timezone'))->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3"><code class="text-xs font-semibold">{{ $log->action }}</code></td>
                        <td class="px-4 py-3">{{ $log->actor_label }}</td>
                        <td class="px-4 py-3">
                            @if ($log->changes || $log->context)
                                <details>
                                    <summary class="cursor-pointer text-secondary-strong">{{ __('Show') }}</summary>
                                    <pre class="mt-2 max-w-md overflow-x-auto rounded-lg bg-surface-alt p-3 text-xs dark:bg-zinc-800">{{ json_encode(array_filter(['changes' => $log->changes, 'context' => $log->context]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-ink-soft">{{ __('No matching entries.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
