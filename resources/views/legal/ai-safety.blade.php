<x-legal-page :title="$pageTitle" :version="$version">
    <p>{{ __(':module uses artificial intelligence to explain questions. Here is what that means for you.', ['module' => $brand->get('flagship_module')]) }}</p>

    <h2>{{ __('AI can make mistakes') }}</h2>
    <p>{{ __('Explanations may sometimes be wrong, incomplete or misread a photo. Check important answers with your teacher or textbook, and use the "Report" button on any answer you think is wrong.') }}</p>

    <h2>{{ __('Learning first') }}</h2>
    <p>{{ __('The tutor is designed to help you understand. It offers hints and guided steps, and it will not help with a live examination where outside help is not allowed.') }}</p>

    <h2>{{ __('Not professional advice') }}</h2>
    <p>{{ __('The tutor is for school subjects. It does not give medical, legal or financial advice.') }}</p>

    <h2>{{ __('Sources') }}</h2>
    <p>{{ __('When an explanation is based on approved learning material we show where it came from. If no reliable source is available, the tutor should say it is unsure rather than invent one.') }}</p>

    <h2>{{ __('No affiliation') }}</h2>
    <p>{{ __(':product is not affiliated with or endorsed by WAEC, NECO or JAMB.', ['product' => $brand->get('product_name')]) }}</p>
</x-legal-page>
