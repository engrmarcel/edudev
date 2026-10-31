<x-layouts.app :title="__('Dashboard')">
    <div class="mx-auto w-full max-w-6xl space-y-8 p-4 sm:p-6 lg:p-8">
        <section class="relative overflow-hidden rounded-3xl bg-primary p-6 text-white sm:p-10">
            <div aria-hidden="true" class="pointer-events-none absolute -top-20 -right-10 size-72 rounded-full bg-secondary/25 blur-3xl"></div>
            <p class="relative text-sm font-medium text-gold">{{ now()->timezone(config('edudev.locale.display_timezone'))->format('l, j F') }}</p>
            <h1 class="relative mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">
                {{ __('Welcome, :name', ['name' => Str::of(auth()->user()->name)->before(' ')]) }}
            </h1>
            <p class="relative mt-2 max-w-xl text-white/80">{{ $brand->get('slogan') }}</p>
        </section>

        @php($accountType = auth()->user()->profile?->account_type)
        @if ($accountType && $accountType !== \App\Enums\AccountType::Student)
            <section class="card" aria-labelledby="portal">
                <p class="eyebrow">{{ __($accountType->label()) }}</p>
                <h2 id="portal" class="mt-2 text-lg font-bold text-ink dark:text-white">{{ __('Your tools are on the way') }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-ink-soft dark:text-zinc-400">
                    {{ __($accountType->description()) }}
                    {{ __('These features are still being built. Your account is set up, and we will let you know as soon as they are available.') }}
                </p>
                @if ($accountType->isOrganisation())
                    <p class="mt-4 inline-flex items-center gap-2 rounded-lg bg-info-soft px-3 py-2 text-sm text-info">
                        <flux:icon name="information-circle" variant="mini" /> {{ __('Organisation verification pending.') }}
                    </p>
                @endif
                <p class="mt-4"><a href="{{ route('onboarding') }}" class="text-sm font-medium text-secondary-strong hover:underline" wire:navigate>{{ __('Change account type') }}</a></p>
            </section>
        @else
        <section aria-labelledby="getting-started">
            <h2 id="getting-started" class="text-lg font-bold text-ink dark:text-white">{{ __('Your learning') }}</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                @foreach ([
                    ['academic-cap', 'Subjects and goals', 'Choose your exam and subjects so we can tailor practice to you.'],
                    ['chat-bubble-left-right', 'Tutor sessions', 'Your conversations with the AI Tutor will appear here.'],
                    ['chart-bar', 'Progress', 'Topic-by-topic progress appears once you start practising.'],
                ] as [$icon, $heading, $text])
                    <div class="card">
                        <flux:icon :name="$icon" class="size-6 text-secondary-strong" />
                        <h3 class="mt-4 font-semibold text-ink dark:text-white">{{ __($heading) }}</h3>
                        <p class="mt-1 text-sm text-ink-soft dark:text-zinc-400">{{ __($text) }}</p>
                        <p class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-surface-alt px-2.5 py-1 text-xs font-medium text-ink-soft dark:bg-zinc-800 dark:text-zinc-300">
                            <flux:icon name="clock" variant="micro" /> {{ __('Coming soon') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</x-layouts.app>
