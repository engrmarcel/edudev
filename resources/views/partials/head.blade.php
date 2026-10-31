<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="theme-color" content="{{ $brand->get('colors.primary') }}" />

@php($pageTitle = isset($title) && $title ? $title.' · '.$brand->get('product_name') : $brand->get('product_name').' — '.$brand->get('slogan'))
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription ?? $brand->get('product_name').' is an '.$brand->get('description').' for WAEC, NECO and JAMB preparation, CBT practice and school learning.' }}" />
<meta property="og:title" content="{{ $pageTitle }}" />
<meta property="og:site_name" content="{{ $brand->get('product_name') }}" />
<meta property="og:type" content="website" />

<link rel="icon" href="{{ $brand->get('favicon_path') }}" type="image/svg+xml" />
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@if ($css = $brand->cssVariables())
    <style>{!! $css !!}</style>
@endif
@fluxAppearance
