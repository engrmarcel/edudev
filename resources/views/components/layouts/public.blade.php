@props(['title' => null, 'metaDescription' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title, 'metaDescription' => $metaDescription])
    </head>
    <body class="min-h-screen bg-surface text-ink antialiased">
        <a href="#main" class="skip-link">{{ __('Skip to main content') }}</a>

        <header class="sticky top-0 z-40 border-b border-line/70 bg-surface/90 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6" aria-label="{{ __('Main') }}">
                <a href="{{ route('home') }}" class="shrink-0">
                    <x-app-logo />
                </a>

                <ul class="hidden items-center gap-7 text-sm font-medium text-ink-soft md:flex">
                    <li><a class="hover:text-primary" href="{{ route('home') }}#tutor">{{ __('AI Tutor') }}</a></li>
                    <li><a class="hover:text-primary" href="{{ route('home') }}#exams">{{ __('Exam prep') }}</a></li>
                    <li><a class="hover:text-primary" href="{{ route('home') }}#institutions">{{ __('Schools & CBT centres') }}</a></li>
                    <li><a class="hover:text-primary" href="{{ route('home') }}#faq">{{ __('FAQ') }}</a></li>
                </ul>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn px-3 text-ink hover:text-primary">{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">{{ __('Start free') }}</a>
                        @endif
                    @endauth
                </div>
            </nav>
        </header>

        <main id="main">
            {{ $slot }}
        </main>

        <footer class="border-t border-line bg-surface-alt">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-4">
                <div class="md:col-span-2">
                    <x-app-logo />
                    <p class="mt-4 max-w-sm text-sm text-ink-soft">{{ $brand->get('slogan') }}</p>
                    <p class="mt-4 text-sm text-ink-soft">
                        {{ __('Support:') }}
                        <a class="font-medium text-primary underline-offset-2 hover:underline" href="mailto:{{ $brand->get('support_email') }}">{{ $brand->get('support_email') }}</a>
                    </p>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-ink">{{ __('Legal') }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                        <li><a class="hover:text-primary" href="{{ $brand->termsUrl() }}">{{ __('Terms of service') }}</a></li>
                        <li><a class="hover:text-primary" href="{{ $brand->privacyUrl() }}">{{ __('Privacy policy') }}</a></li>
                        <li><a class="hover:text-primary" href="{{ route('legal.show', 'acceptable-use') }}">{{ __('Acceptable use') }}</a></li>
                        <li><a class="hover:text-primary" href="{{ route('legal.show', 'refunds') }}">{{ __('Refunds & cancellation') }}</a></li>
                        <li><a class="hover:text-primary" href="{{ route('legal.show', 'ai-safety') }}">{{ __('AI safety & limitations') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-ink">{{ __('Follow us') }}</h2>
                    @php($social = array_filter((array) $brand->get('social', [])))
                    @if ($social)
                        <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                            @foreach ($social as $network => $url)
                                <li><a class="capitalize hover:text-primary" href="{{ $url }}" rel="noopener" target="_blank">{{ $network === 'x' ? 'X' : $network }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-sm text-ink-soft">{{ __('Social channels coming soon.') }}</p>
                    @endif
                </div>
            </div>

            <div class="border-t border-line">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-xs text-ink-soft sm:px-6 md:flex-row md:justify-between">
                    <p>&copy; {{ now()->year }} {{ $brand->get('product_name') }}@if ($brand->get('show_footer_attribution')) · {{ __('A :company product', ['company' => $brand->get('company_name')]) }}@endif</p>
                    <p>{{ __(':product is an independent learning platform and is not affiliated with or endorsed by WAEC, NECO or JAMB.', ['product' => $brand->get('product_name')]) }}</p>
                </div>
            </div>
        </footer>
    </body>
</html>
