<?php

use App\Enums\AccountType;
use App\Enums\Role;
use App\Models\User;
use Livewire\Volt\Volt;

function registerUser(array $overrides = [])
{
    return Volt::test('auth.register')
        ->set('name', $overrides['name'] ?? 'Ada Obi')
        ->set('email', $overrides['email'] ?? 'ada@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('terms', $overrides['terms'] ?? true)
        ->call('register');
}

it('requires agreement to the terms and privacy policy', function () {
    registerUser(['terms' => false])->assertHasErrors('terms');

    expect(User::query()->count())->toBe(0);
});

it('records the accepted policy versions at registration', function () {
    registerUser()->assertHasNoErrors();

    $user = User::query()->where('email', 'ada@example.com')->first();
    $accepted = $user->policyAcceptances()->pluck('version', 'policy')->all();

    expect($accepted)->toEqualCanonicalizing([
        'terms' => config('edudev.policies.terms'),
        'privacy' => config('edudev.policies.privacy'),
    ]);
});

it('sends new users to onboarding, not the dashboard', function () {
    registerUser()->assertRedirect(route('onboarding', absolute: false));

    expect(User::query()->first()->roles)->toHaveCount(0);
});

it('onboards a student with class and exam goals', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('onboarding')
        ->set('accountType', 'student')
        ->set('classLevel', 'ss3')
        ->set('examGoals', ['waec', 'jamb'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user->refresh();
    expect($user->hasRole(Role::Student->value))->toBeTrue()
        ->and($user->profile->account_type)->toBe(AccountType::Student)
        ->and($user->profile->exam_goals)->toBe(['waec', 'jamb'])
        ->and($user->profile->onboarded_at)->not->toBeNull();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

it('requires the right fields for each account type', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('onboarding')
        ->set('accountType', 'student')
        ->call('save')
        ->assertHasErrors(['classLevel', 'examGoals']);

    Volt::actingAs($user)->test('onboarding')
        ->set('accountType', 'school')
        ->call('save')
        ->assertHasErrors(['organisationName', 'state']);
});

it('onboards a school administrator and keeps only relevant fields', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('onboarding')
        ->set('accountType', 'school')
        ->set('organisationName', 'Unity Secondary School')
        ->set('state', 'Lagos')
        ->set('classLevel', 'ss3')  // not relevant to a school; must be discarded
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->hasRole(Role::SchoolAdmin->value))->toBeTrue()
        ->and($user->profile->organisation_name)->toBe('Unity Secondary School')
        ->and($user->profile->class_level)->toBeNull();
});

it('cannot be used to pick a staff role', function (string $value) {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('onboarding')
        ->set('accountType', $value)
        ->call('save')
        ->assertHasErrors('accountType');

    expect($user->fresh()->roles)->toHaveCount(0);
})->with(['super_admin', 'platform_admin', 'content_manager', 'support_agent']);

it('swaps the role when the account type changes', function () {
    $user = userWithRole(Role::Student->value);

    Volt::actingAs($user)->test('onboarding')
        ->set('accountType', 'guardian')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->hasRole(Role::Guardian->value))->toBeTrue()
        ->and($user->hasRole(Role::Student->value))->toBeFalse();
});

it('redirects each kind of account to the right home after sign-in', function () {
    expect(userWithRole(Role::Student->value)->homeUrl())->toBe(route('dashboard', absolute: false))
        ->and(userWithRole(Role::Teacher->value)->homeUrl())->toBe(route('dashboard', absolute: false))
        ->and(userWithRole(Role::PlatformAdmin->value)->homeUrl())->toBe(route('admin.dashboard', absolute: false))
        ->and(User::factory()->create()->homeUrl())->toBe(route('onboarding', absolute: false));
});

it('shows non-student account types an honest "coming soon" dashboard', function () {
    $this->actingAs(userWithRole(Role::Teacher->value))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Your tools are on the way');
});
