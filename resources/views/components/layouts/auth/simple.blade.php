<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-surface text-ink antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div class="grid min-h-svh lg:grid-cols-2">
            <aside class="relative hidden overflow-hidden bg-primary p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div aria-hidden="true" class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-secondary/20 blur-3xl"></div>
                <div aria-hidden="true" class="pointer-events-none absolute -bottom-32 -left-16 size-96 rounded-full bg-gold/15 blur-3xl"></div>

                <a href="{{ route('home') }}" class="relative flex items-center gap-3" wire:navigate>
                    <x-app-logo-icon class="size-10" />
                    <span class="text-xl font-bold tracking-tight">{{ $brand->get('product_name') }}</span>
                </a>

                <div class="relative max-w-md">
                    <p class="text-4xl leading-tight font-extrabold tracking-tight">{{ $brand->get('slogan') }}</p>
                    <p class="mt-4 text-base text-white/80">
                        {{ __('Understand concepts step by step, practise under exam conditions and see exactly what to study next.') }}
                    </p>
                </div>

                <p class="relative text-sm text-white/60">&copy; {{ now()->year }} {{ $brand->get('company_name') }}</p>
            </aside>

            <main id="main" class="flex flex-col items-center justify-center gap-6 p-6 md:p-10">
                <div class="flex w-full max-w-sm flex-col gap-2">
                    <a href="{{ route('home') }}" class="mb-4 flex justify-center lg:hidden" wire:navigate>
                        <x-app-logo />
                        <span class="sr-only">{{ $brand->get('product_name') }}</span>
                    </a>
                    <div class="flex flex-col gap-6">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
        @fluxScripts
    </body>
</html>
