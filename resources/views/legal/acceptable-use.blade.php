<x-legal-page :title="$pageTitle" :version="$version">
    <p>{{ __('To keep :product safe and useful for every learner, you agree not to:', ['product' => $brand->get('product_name')]) }}</p>
    <ul>
        <li>{{ __('Use the tutor to get help during a live, supervised examination where outside help is not allowed.') }}</li>
        <li>{{ __('Upload content you do not have the right to share, or content that is harmful, abusive or explicit.') }}</li>
        <li>{{ __('Try to access other users\' accounts, data or answer keys.') }}</li>
        <li>{{ __('Interfere with the service, bypass usage limits, or use automated tools to extract content.') }}</li>
        <li>{{ __('Share or resell your account or institutional access codes.') }}</li>
    </ul>
    <p>{{ __('We may limit, suspend or close accounts that break these rules.') }}</p>
</x-legal-page>
