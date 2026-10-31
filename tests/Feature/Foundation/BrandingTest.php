<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\Branding\BrandManager;
use Livewire\Volt\Volt;

it('shows the default brand on the homepage', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('EduDEV')
        ->assertSee('Learn Smarter. Achieve More.')
        ->assertSee('not affiliated with or endorsed by WAEC, NECO or JAMB');
});

it('applies admin overrides everywhere without code changes', function () {
    $admin = userWithRole(Role::SuperAdmin->value);

    Volt::actingAs($admin)->test('admin.branding')
        ->set('form.product_name', 'StudyPro')
        ->set('form.colors__primary', '#112233')
        ->call('save')
        ->assertHasNoErrors();

    $this->get('/')
        ->assertSee('StudyPro')
        ->assertSee('--brand-primary: #112233;', false);

    expect(AuditLog::query()->where('action', 'branding.updated')->exists())->toBeTrue();
});

it('validates colour values so they cannot inject CSS', function () {
    $admin = userWithRole(Role::SuperAdmin->value);

    Volt::actingAs($admin)->test('admin.branding')
        ->set('form.colors__primary', 'red;}</style><script>alert(1)</script>')
        ->call('save')
        ->assertHasErrors('form.colors__primary');
});

it('never prints an invalid stored colour', function () {
    SystemSetting::query()->create(['group' => 'brand', 'key' => 'colors.primary', 'value' => 'red;}body{x']);
    app(BrandManager::class)->flush();

    expect(app(BrandManager::class)->cssVariables())->not->toContain('red;');
});

it('refuses to edit keys that are not on the allow-list', function () {
    app(BrandManager::class)->update(['logo_path' => 'https://evil.test/x.svg']);
})->throws(InvalidArgumentException::class);

it('removes overrides when values are reset to defaults', function () {
    $brand = app(BrandManager::class);
    $brand->update(['slogan' => 'Something else']);
    expect($brand->get('slogan'))->toBe('Something else');

    $brand->update(['slogan' => null]);
    expect($brand->get('slogan'))->toBe('Learn Smarter. Achieve More.');
    expect(SystemSetting::query()->count())->toBe(0);
});
