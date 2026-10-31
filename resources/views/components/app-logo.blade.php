@props(['subtitle' => true])
@php($logo = $brand->get('logo_path'))
<span class="flex items-center gap-2.5">
    @if ($logo)
        <img src="{{ $logo }}" alt="" class="size-8 rounded-lg object-contain" />
    @else
        <x-app-logo-icon class="size-8 shrink-0" />
    @endif
    <span class="grid text-left leading-tight">
        <span class="truncate text-base font-bold tracking-tight text-primary dark:text-white">{{ $brand->get('product_name') }}</span>
        @if ($subtitle)
            <span class="truncate text-[11px] font-medium text-muted dark:text-zinc-400">{{ $brand->get('description') }}</span>
        @endif
    </span>
</span>
