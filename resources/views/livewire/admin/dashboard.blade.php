<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Admin overview')] class extends Component {
    public function with(): array
    {
        $since = now()->subDays(30);

        $roleCounts = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', (new User)->getMorphClass())
            ->groupBy('roles.name')
            ->pluck(DB::raw('count(*)'), 'roles.name');

        return [
            'stats' => [
                ['label' => __('Registered users'), 'value' => User::query()->count(), 'note' => __('All time')],
                ['label' => __('New users'), 'value' => User::query()->where('created_at', '>=', $since)->count(), 'note' => __('Last 30 days')],
                ['label' => __('Signed in'), 'value' => User::query()->where('last_login_at', '>=', $since)->count(), 'note' => __('Last 30 days')],
                ['label' => __('Suspended'), 'value' => User::query()->where('status', User::STATUS_SUSPENDED)->count(), 'note' => __('Current')],
            ],
            'roles' => collect(Role::cases())->map(fn (Role $r) => ['label' => $r->label(), 'count' => (int) ($roleCounts[$r->value] ?? 0)]),
            'recent' => AuditLog::query()->latest('id')->limit(8)->get(['id', 'action', 'actor_label', 'created_at']),
        ];
    }
}; ?>

<div class="mx-auto w-full max-w-6xl space-y-8 p-4 sm:p-6 lg:p-8">
    <header>
        <p class="eyebrow">{{ __('Administration') }}</p>
        <h1 class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ __('Platform overview') }}</h1>
        <p class="mt-1 text-sm text-ink-soft dark:text-zinc-400">{{ __('Live figures from the database. Revenue, AI usage and exam figures will appear here as those modules are built.') }}</p>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="{{ __('Key figures') }}">
        @foreach ($stats as $s)
            <div class="card">
                <p class="text-sm font-medium text-ink-soft dark:text-zinc-400">{{ $s['label'] }}</p>
                <p class="mt-2 text-3xl font-extrabold text-primary dark:text-white">{{ number_format($s['value']) }}</p>
                <p class="mt-1 text-xs text-muted">{{ $s['note'] }}</p>
            </div>
        @endforeach
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="card">
            <h2 class="font-bold text-ink dark:text-white">{{ __('Accounts by role') }}</h2>
            <table class="mt-4 w-full text-sm">
                <thead class="sr-only"><tr><th>{{ __('Role') }}</th><th>{{ __('Accounts') }}</th></tr></thead>
                <tbody class="divide-y divide-line dark:divide-zinc-800">
                    @foreach ($roles as $r)
                        <tr><td class="py-2 text-ink-soft dark:text-zinc-300">{{ $r['label'] }}</td><td class="py-2 text-right font-semibold tabular-nums">{{ number_format($r['count']) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="card">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-ink dark:text-white">{{ __('Recent sensitive activity') }}</h2>
                @can(\App\Enums\Permission::ViewAuditLogs->value)
                    <a href="{{ route('admin.audit') }}" class="text-sm font-medium text-secondary-strong hover:underline" wire:navigate>{{ __('View all') }}</a>
                @endcan
            </div>
            @forelse ($recent as $log)
                <div class="flex justify-between gap-4 border-b border-line py-2 text-sm last:border-0 dark:border-zinc-800">
                    <span><code class="text-xs font-semibold">{{ $log->action }}</code> <span class="text-ink-soft dark:text-zinc-400">· {{ $log->actor_label }}</span></span>
                    <time class="shrink-0 text-xs text-muted" datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->timezone(config('edudev.locale.display_timezone'))->diffForHumans() }}</time>
                </div>
            @empty
                <p class="mt-4 text-sm text-ink-soft">{{ __('Nothing recorded yet.') }}</p>
            @endforelse
        </section>
    </div>
</div>
