<x-legal-page :title="$pageTitle" :version="$version">
    <p>{{ __('This policy explains what personal data :company collects through :product, why, and the choices you have. It is being prepared with reference to the Nigeria Data Protection Act 2023 and will be reviewed by qualified counsel.', ['product' => $brand->get('product_name'), 'company' => $brand->get('company_name')]) }}</p>

    <h2>{{ __('What we collect') }}</h2>
    <ul>
        <li>{{ __('Account details: name, email address, and the role you sign up as.') }}</li>
        <li>{{ __('Learning activity: questions you ask, practice answers, exam attempts and progress records.') }}</li>
        <li>{{ __('Uploaded images of questions you choose to submit.') }}</li>
        <li>{{ __('Payment records (amount, date, status). Card details are handled by our payment provider and are not stored by us.') }}</li>
        <li>{{ __('Technical data needed for security, such as IP address and device type.') }}</li>
    </ul>

    <h2>{{ __('Why we use it') }}</h2>
    <ul>
        <li>{{ __('To provide tutoring, practice, progress tracking and study plans.') }}</li>
        <li>{{ __('To process subscriptions and keep financial records.') }}</li>
        <li>{{ __('To protect accounts and the service from misuse.') }}</li>
    </ul>

    <h2>{{ __('AI providers') }}</h2>
    <p>{{ __('To generate explanations, the content of your question is sent to a third-party AI provider. We design the service to avoid sending your name or contact details with it. [Named providers and safeguards to be listed before launch.]') }}</p>

    <h2>{{ __('Children and students') }}</h2>
    <p>{{ __('Parents and guardians can only see a student\'s progress through a linked account the student or institution has approved. Schools see records for students enrolled with them.') }}</p>

    <h2>{{ __('Your rights') }}</h2>
    <p>{{ __('You can ask to see, correct or delete your personal data, or object to some uses of it, by contacting us. [Response times and procedure to be confirmed.]') }}</p>

    <h2>{{ __('Retention') }}</h2>
    <p>{{ __('[Retention periods for each type of data to be defined before launch.]') }}</p>
</x-legal-page>
