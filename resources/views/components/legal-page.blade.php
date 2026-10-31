@props(['title', 'version' => null])
<x-layouts.public :title="$title">
    <article class="mx-auto max-w-3xl px-4 py-14 sm:px-6">
        <div role="note" class="mb-8 rounded-xl border border-warning/30 bg-warning-soft p-4 text-sm text-warning">
            <strong>{{ __('Draft for legal review.') }}</strong>
            {{ __('This document has not yet been approved by a qualified lawyer and is not in force. It will be finalised before public launch.') }}
        </div>

        <h1 class="text-3xl font-extrabold tracking-tight text-primary sm:text-4xl">{{ __($title) }}</h1>
        @if ($version)
            <p class="mt-2 text-sm text-muted">{{ __('Version') }}: {{ $version }}</p>
        @endif

        <div class="mt-8 space-y-6 leading-relaxed text-ink-soft [&_h2]:mt-10 [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-ink [&_li]:ml-5 [&_li]:list-disc [&_ul]:space-y-2">
            {{ $slot }}
        </div>

        <p class="mt-12 border-t border-line pt-6 text-sm text-muted">
            {{ __('Questions about this policy? Contact') }}
            <a class="font-medium text-primary hover:underline" href="mailto:{{ $brand->get('support_email') }}">{{ $brand->get('support_email') }}</a>.
        </p>
    </article>
</x-layouts.public>
