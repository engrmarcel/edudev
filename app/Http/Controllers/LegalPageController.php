<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Public policy pages. Content lives in resources/views/legal/*.blade.php.
 * Every page is a DRAFT until reviewed by a qualified Nigerian lawyer.
 */
class LegalPageController extends Controller
{
    public const PAGES = [
        'terms' => 'Terms of Service',
        'privacy' => 'Privacy Policy',
        'acceptable-use' => 'Acceptable Use Policy',
        'refunds' => 'Refund and Cancellation Policy',
        'ai-safety' => 'AI Safety and Limitations',
    ];

    public function __invoke(string $page): View
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);

        return view('legal.'.$page, [
            'pageTitle' => self::PAGES[$page],
            'version' => config('edudev.policies.'.($page === 'privacy' ? 'privacy' : 'terms')),
        ]);
    }
}
