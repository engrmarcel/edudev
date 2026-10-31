<?php

use App\Enums\Permission;
use App\Services\Branding\BrandManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Branding')] class extends Component {
    /** @var array<string, mixed> form state, keyed with "__" instead of "." */
    public array $form = [];

    public function mount(BrandManager $brand): void
    {
        Gate::authorize(Permission::ManageBranding->value);

        foreach (config('edudev.editable_brand_keys') as $key) {
            $this->form[$this->field($key)] = $brand->get($key);
        }
    }

    public function save(BrandManager $brand): void
    {
        // Re-check on every action: Livewire actions are separate requests.
        Gate::authorize(Permission::ManageBranding->value);

        $hex = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];
        $url = ['nullable', 'url:http,https', 'max:255'];

        $rules = [
            'form.product_name' => ['required', 'string', 'max:60'],
            'form.company_name' => ['required', 'string', 'max:120'],
            'form.flagship_module' => ['required', 'string', 'max:80'],
            'form.slogan' => ['required', 'string', 'max:120'],
            'form.description' => ['required', 'string', 'max:160'],
            'form.colors__primary' => ['required', ...array_slice($hex, 1)],
            'form.colors__secondary' => ['required', ...array_slice($hex, 1)],
            'form.colors__accent' => ['required', ...array_slice($hex, 1)],
            'form.support_email' => ['required', 'email', 'max:255'],
            'form.contact_phone' => ['nullable', 'string', 'max:40'],
            'form.contact_address' => ['nullable', 'string', 'max:255'],
            'form.website_url' => $url,
            'form.terms_url' => $url,
            'form.privacy_url' => $url,
            'form.show_footer_attribution' => ['boolean'],
        ];
        foreach (['facebook', 'x', 'instagram', 'linkedin', 'youtube', 'tiktok'] as $net) {
            $rules["form.social__{$net}"] = $url;
        }

        $this->validate($rules);

        $values = [];
        foreach (config('edudev.editable_brand_keys') as $key) {
            $value = $this->form[$this->field($key)] ?? null;
            $values[$key] = is_string($value) ? trim($value) : $value;
        }
        $values['show_footer_attribution'] = (bool) $values['show_footer_attribution'];

        $brand->update($values, auth()->user());

        $this->dispatch('branding-saved');
    }

    public function resetToDefaults(BrandManager $brand): void
    {
        Gate::authorize(Permission::ManageBranding->value);

        $brand->update(array_fill_keys(config('edudev.editable_brand_keys'), null), auth()->user());

        $this->redirectRoute('admin.branding', navigate: true);
    }

    private function field(string $key): string
    {
        return str_replace('.', '__', $key);
    }
}; ?>

<div class="mx-auto w-full max-w-4xl space-y-8 p-4 sm:p-6 lg:p-8">
    <header>
        <p class="eyebrow">{{ __('Administration') }}</p>
        <h1 class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ __('Branding') }}</h1>
        <p class="mt-1 text-sm text-ink-soft dark:text-zinc-400">{{ __('Changes apply across the website, app and emails immediately and are recorded in the audit log. Leave a field as it is to keep the default.') }}</p>
    </header>

    <form wire:submit="save" class="space-y-8">
        <section class="card space-y-5">
            <h2 class="font-bold">{{ __('Identity') }}</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model="form.product_name" :label="__('Product name')" required />
                <flux:input wire:model="form.company_name" :label="__('Company name')" required />
                <flux:input wire:model="form.flagship_module" :label="__('Flagship module name')" required />
                <flux:input wire:model="form.slogan" :label="__('Slogan')" required />
            </div>
            <flux:input wire:model="form.description" :label="__('Short description')" required />
        </section>

        <section class="card space-y-5">
            <h2 class="font-bold">{{ __('Colours') }}</h2>
            <p class="text-sm text-ink-soft">{{ __('Use 6-digit hex values such as #0B2545. Check text contrast before saving: the primary colour must work behind white text.') }}</p>
            <div class="grid gap-5 sm:grid-cols-3">
                @foreach (['primary' => __('Primary'), 'secondary' => __('Secondary'), 'accent' => __('Accent')] as $c => $label)
                    <div class="flex items-end gap-3">
                        <span class="mb-1 size-10 shrink-0 rounded-lg ring-1 ring-line" style="background: {{ preg_match('/^#[0-9A-Fa-f]{6}$/', (string) ($form['colors__'.$c] ?? '')) ? $form['colors__'.$c] : 'transparent' }}" aria-hidden="true"></span>
                        <flux:input wire:model.live.debounce.400ms="form.colors__{{ $c }}" :label="$label" required class="flex-1" />
                    </div>
                @endforeach
            </div>
        </section>

        <section class="card space-y-5">
            <h2 class="font-bold">{{ __('Contact and links') }}</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model="form.support_email" type="email" :label="__('Support email')" required />
                <flux:input wire:model="form.contact_phone" :label="__('Contact phone')" />
                <flux:input wire:model="form.website_url" type="url" :label="__('Website URL')" />
                <flux:input wire:model="form.contact_address" :label="__('Address')" />
                <flux:input wire:model="form.terms_url" type="url" :label="__('External terms URL (optional)')" />
                <flux:input wire:model="form.privacy_url" type="url" :label="__('External privacy URL (optional)')" />
            </div>
        </section>

        <section class="card space-y-5">
            <h2 class="font-bold">{{ __('Social media') }}</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach (['facebook' => 'Facebook', 'x' => 'X', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $net => $label)
                    <flux:input wire:model="form.social__{{ $net }}" type="url" :label="$label" placeholder="https://" />
                @endforeach
            </div>
            <flux:checkbox wire:model="form.show_footer_attribution" :label="__('Show company attribution in the footer')" />
        </section>

        <div class="flex flex-wrap items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save branding') }}</flux:button>
            <x-action-message on="branding-saved">{{ __('Saved.') }}</x-action-message>
            <flux:spacer />
            <flux:button variant="ghost" type="button" wire:click="resetToDefaults" wire:confirm="{{ __('Reset all branding to the default values?') }}">{{ __('Reset to defaults') }}</flux:button>
        </div>
    </form>
</div>
