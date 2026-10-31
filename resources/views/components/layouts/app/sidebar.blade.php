<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-surface-alt text-ink antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <a href="#main" class="skip-link">{{ __('Skip to main content') }}</a>

        <flux:sidebar sticky stashable class="border-r border-line bg-surface dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Learn')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                @can(\App\Enums\Permission::AccessAdmin->value)
                    <flux:navlist.group :heading="__('Administration')" class="mt-4 grid">
                        <flux:navlist.item icon="chart-bar" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Overview') }}</flux:navlist.item>
                        @can(\App\Enums\Permission::ManageUsers->value)
                            <flux:navlist.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                            <flux:navlist.item icon="user-minus" :href="route('admin.deletion-requests')" :current="request()->routeIs('admin.deletion-requests')" wire:navigate>{{ __('Deletion requests') }}</flux:navlist.item>
                        @endcan
                        @can(\App\Enums\Permission::ManageBranding->value)
                            <flux:navlist.item icon="swatch" :href="route('admin.branding')" :current="request()->routeIs('admin.branding')" wire:navigate>{{ __('Branding') }}</flux:navlist.item>
                        @endcan
                        @can(\App\Enums\Permission::ViewAuditLogs->value)
                            <flux:navlist.item icon="clipboard-document-list" :href="route('admin.audit')" :current="request()->routeIs('admin.audit')" wire:navigate>{{ __('Audit log') }}</flux:navlist.item>
                        @endcan
                    </flux:navlist.group>
                @endcan
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="lifebuoy" href="mailto:{{ $brand->get('support_email') }}">{{ __('Get help') }}</flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    @include('partials.user-menu')
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="border-b border-line bg-surface lg:hidden dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" :aria-label="__('Open menu')" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    @include('partials.user-menu')
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <div id="main" class="contents">
            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
