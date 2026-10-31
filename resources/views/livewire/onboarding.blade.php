<?php

use App\Enums\AccountType;
use App\Services\Accounts\OnboardingService;
use App\Support\NigeriaReference;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] #[Title('Set up your account')] class extends Component {
    public string $accountType = '';
    public string $classLevel = '';
    public array $examGoals = [];
    public string $organisationName = '';
    public string $state = '';
    public string $phone = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->isStaff()) {
            $this->redirect($user->homeUrl(), navigate: true);

            return;
        }

        if ($profile = $user->profile) {
            $this->accountType = $profile->account_type->value;
            $this->classLevel = (string) $profile->class_level;
            $this->examGoals = $profile->exam_goals ?? [];
            $this->organisationName = (string) $profile->organisation_name;
            $this->state = (string) $profile->state;
            $this->phone = (string) $profile->phone;
        }
    }

    public function save(OnboardingService $onboarding): void
    {
        $type = AccountType::tryFrom($this->accountType);

        $rules = ['accountType' => ['required', Rule::enum(AccountType::class)]];

        if ($type === AccountType::Student) {
            $rules['classLevel'] = ['required', Rule::in(array_keys(NigeriaReference::CLASS_LEVELS))];
            $rules['examGoals'] = ['required', 'array', 'min:1'];
            $rules['examGoals.*'] = [Rule::in(array_keys(NigeriaReference::EXAM_GOALS))];
        }
        if ($type?->isOrganisation()) {
            $rules['organisationName'] = ['required', 'string', 'max:160'];
            $rules['state'] = ['required', Rule::in(NigeriaReference::STATES)];
            $rules['phone'] = ['nullable', 'string', 'max:32', 'regex:/^[0-9+\-\s()]{7,32}$/'];
        }
        if ($type === AccountType::Teacher) {
            $rules['organisationName'] = ['nullable', 'string', 'max:160'];
        }
        if ($type && ! $type->isOrganisation()) {
            $rules['state'] = ['nullable', Rule::in(NigeriaReference::STATES)];
        }

        $this->validate($rules, [], [
            'accountType' => __('account type'),
            'classLevel' => __('class'),
            'examGoals' => __('exams'),
            'organisationName' => __('name'),
        ]);

        $onboarding->complete(auth()->user(), $type, array_filter([
            'class_level' => $this->classLevel ?: null,
            'exam_goals' => $this->examGoals ? array_values($this->examGoals) : null,
            'organisation_name' => trim($this->organisationName) ?: null,
            'state' => $this->state ?: null,
            'phone' => trim($this->phone) ?: null,
        ], fn ($v) => $v !== null));

        $this->redirect(auth()->user()->fresh()->homeUrl(), navigate: true);
    }

    public function with(): array
    {
        return ['type' => AccountType::tryFrom($this->accountType)];
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Set up your account')" :description="__('Tell us how you will use :product. You can change this later.', ['product' => $brand->get('product_name')])" />

    <form wire:submit="save" class="flex flex-col gap-6">
        <fieldset>
            <legend class="mb-3 text-sm font-medium">{{ __('I am a…') }}</legend>
            <div class="grid gap-2">
                @foreach (AccountType::cases() as $option)
                    <label @class([
                        'flex cursor-pointer gap-3 rounded-xl border p-3 transition',
                        'border-primary bg-primary/5 ring-1 ring-primary dark:border-secondary dark:ring-secondary' => $accountType === $option->value,
                        'border-line hover:bg-surface-alt dark:border-zinc-700 dark:hover:bg-zinc-800' => $accountType !== $option->value,
                    ])>
                        <input type="radio" wire:model.live="accountType" value="{{ $option->value }}" class="mt-1 accent-primary" />
                        <span>
                            <span class="block text-sm font-semibold">{{ __($option->label()) }}</span>
                            <span class="block text-xs text-ink-soft dark:text-zinc-400">{{ __($option->description()) }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <flux:error name="accountType" />
        </fieldset>

        @if ($type === AccountType::Student)
            <flux:select wire:model="classLevel" :label="__('Your class')" :placeholder="__('Choose your class')">
                @foreach (NigeriaReference::CLASS_LEVELS as $value => $label)
                    <flux:select.option :value="$value">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <fieldset>
                <legend class="mb-2 text-sm font-medium">{{ __('What are you preparing for?') }}</legend>
                <div class="grid gap-2">
                    @foreach (NigeriaReference::EXAM_GOALS as $value => $label)
                        <flux:checkbox wire:model="examGoals" :value="$value" :label="$label" />
                    @endforeach
                </div>
                <flux:error name="examGoals" />
            </fieldset>
        @endif

        @if ($type?->isOrganisation())
            <flux:input wire:model="organisationName" :label="$type === AccountType::School ? __('School name') : __('Centre name')" required />
            <flux:input wire:model="phone" type="tel" :label="__('Contact phone (optional)')" autocomplete="tel" />
            <p class="-mt-3 text-xs text-ink-soft">{{ __('We will contact you to verify your organisation before institutional features are switched on.') }}</p>
        @endif

        @if ($type === AccountType::Teacher)
            <flux:input wire:model="organisationName" :label="__('School you teach at (optional)')" />
        @endif

        @if ($type)
            <flux:select wire:model="state" :label="$type->isOrganisation() ? __('State') : __('State (optional)')" :placeholder="__('Choose a state')">
                @foreach (NigeriaReference::STATES as $s)
                    <flux:select.option :value="$s">{{ $s }}</flux:select.option>
                @endforeach
            </flux:select>
        @endif

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Continue') }}</flux:button>
    </form>
</div>
