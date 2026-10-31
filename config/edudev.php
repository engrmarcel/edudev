<?php

/*
|--------------------------------------------------------------------------
| EduDEV platform configuration
|--------------------------------------------------------------------------
|
| These are DEFAULTS. Authorised administrators can override the "brand"
| values at runtime from Admin → Branding (stored in the system_settings
| table). Templates must read branding through the brand() helper, never
| from these keys directly, so overrides are respected.
|
| Never place secrets in this file. Use .env.
|
*/

return [

    'brand' => [
        'product_name' => env('BRAND_PRODUCT_NAME', 'EduDEV'),
        'company_name' => env('BRAND_COMPANY_NAME', 'MacoDev Limited'),
        'flagship_module' => env('BRAND_FLAGSHIP_MODULE', 'EduDEV AI Tutor'),
        'slogan' => env('BRAND_SLOGAN', 'Learn Smarter. Achieve More.'),
        'description' => env('BRAND_DESCRIPTION', 'AI-Powered Learning Platform'),

        'logo_path' => env('BRAND_LOGO_PATH'),       // null = built-in SVG mark
        'favicon_path' => env('BRAND_FAVICON_PATH', '/favicon.svg'),

        'colors' => [
            'primary' => '#0B2545',     // Deep Navy
            'secondary' => '#13A987',   // Education Green
            'accent' => '#F2A541',      // Achievement Gold
        ],

        'support_email' => env('BRAND_SUPPORT_EMAIL', 'support@example.com'),
        'contact_phone' => env('BRAND_CONTACT_PHONE'),
        'contact_address' => env('BRAND_CONTACT_ADDRESS'),
        'website_url' => env('BRAND_WEBSITE_URL', env('APP_URL', 'http://localhost')),

        'terms_url' => null,       // null = built-in /legal/terms page
        'privacy_url' => null,     // null = built-in /legal/privacy page

        'social' => [
            'facebook' => null,
            'x' => null,
            'instagram' => null,
            'linkedin' => null,
            'youtube' => null,
            'tiktok' => null,
        ],

        'show_footer_attribution' => true,
    ],

    'locale' => [
        // Timestamps are STORED in UTC (config/app.php timezone). This is the
        // timezone used to DISPLAY times to users unless they choose another.
        'display_timezone' => env('EDUDEV_DISPLAY_TIMEZONE', 'Africa/Lagos'),
        'currency' => env('EDUDEV_CURRENCY', 'NGN'),
        'currency_locale' => 'en_NG',
    ],

    /*
    | Brand keys that administrators are allowed to edit from the UI.
    | Anything not listed here can only be changed via config/.env.
    */
    'editable_brand_keys' => [
        'product_name', 'company_name', 'flagship_module', 'slogan', 'description',
        'colors.primary', 'colors.secondary', 'colors.accent',
        'support_email', 'contact_phone', 'contact_address', 'website_url',
        'terms_url', 'privacy_url',
        'social.facebook', 'social.x', 'social.instagram', 'social.linkedin', 'social.youtube', 'social.tiktok',
        'show_footer_attribution',
    ],

    /*
    | Staff/admin area. Admins sign in at /{path}/login, separate from the
    | student/user /login page. Change ADMIN_PATH in production to a
    | non-obvious value (letters, numbers, dashes) to reduce automated attacks.
    */
    'admin' => [
        'path' => trim((string) env('ADMIN_PATH', 'admin'), '/') ?: 'admin',
    ],

    /*
    | Two-step login (authenticator app codes + recovery codes).
    | OFF by default in local/testing; ON by default when APP_ENV=production.
    | TWO_FACTOR_ENABLED=true|false always overrides. When enabled:
    |  - every user can turn it on in Settings → Two-step login;
    |  - roles listed in "required_roles" MUST set it up before using the
    |    app (default: all staff roles).
    */
    'two_factor' => [
        'enabled' => (bool) env('TWO_FACTOR_ENABLED', env('APP_ENV') === 'production'),
        'required_roles' => array_values(array_filter(array_map('trim', explode(',', (string) env(
            'TWO_FACTOR_REQUIRED_ROLES',
            'super_admin,platform_admin,content_manager,support_agent',
        ))))),
        'recovery_codes' => 8,
        // Window of +/- N 30-second steps accepted for clock drift.
        'window' => 1,
        // How long a half-finished sign-in waits for the code.
        'challenge_ttl_minutes' => 10,
    ],

    // Policy versions users accept at registration. Bump when the text changes.
    'policies' => [
        'terms' => '2026-10-draft',
        'privacy' => '2026-10-draft',
    ],

];
