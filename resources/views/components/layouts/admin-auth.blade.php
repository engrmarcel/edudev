<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <meta name="robots" content="noindex, nofollow" />
    </head>
    <body class="min-h-screen bg-primary text-ink antialiased">
        <main id="main" class="flex min-h-svh flex-col items-center justify-center gap-8 p-6">
            <div class="flex items-center gap-3 text-white">
                <x-app-logo-icon class="size-10 ring-1 ring-white/20 rounded-xl" />
                <span class="text-xl font-bold tracking-tight">{{ $brand->get('product_name') }} <span class="font-medium text-white/60">{{ __('Admin') }}</span></span>
            </div>

            <div class="w-full max-w-sm rounded-2xl bg-surface p-8 shadow-2xl shadow-black/30">
                {{ $slot }}
            </div>

            <p class="max-w-sm text-center text-xs text-white/60">
                {{ __('Sign-in attempts on this page are logged.') }}
            </p>
        </main>
        @fluxScripts
    </body>
</html>
