<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $heading])
        <meta name="robots" content="noindex" />
    </head>
    <body class="min-h-screen bg-surface-alt text-ink antialiased">
        <main id="main" class="flex min-h-svh flex-col items-center justify-center p-6 text-center">
            <a href="{{ url('/') }}"><x-app-logo /></a>
            <p class="mt-10 text-6xl font-extrabold text-primary">{{ $code }}</p>
            <h1 class="mt-4 text-2xl font-bold">{{ $heading }}</h1>
            <p class="mt-2 max-w-md text-ink-soft">{{ $message }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ url('/') }}" class="btn-primary">{{ __('Go to homepage') }}</a>
                <a href="mailto:{{ $brand->get('support_email') }}" class="btn-outline">{{ __('Contact support') }}</a>
            </div>
            @if ($rid = request()->attributes->get('request_id'))
                <p class="mt-8 text-xs text-ink-soft">{{ __('Reference') }}: <code>{{ $rid }}</code></p>
            @endif
        </main>
    </body>
</html>
