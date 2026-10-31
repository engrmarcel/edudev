<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Accounts\AccountAdministration;
use App\Services\Auth\TwoFactorService;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    config(['edudev.two_factor.enabled' => true]);
});

function currentCode(User $user): string
{
    return app(Google2FA::class)->getCurrentOtp($user->fresh()->two_factor_secret);
}

/** Turn two-step login on for a user and return their recovery codes. */
function enableTwoFactor(User $user): array
{
    $service = app(TwoFactorService::class);
    $service->begin($user);

    return $service->confirm($user->fresh(), currentCode($user));
}

it('is off by default outside production', function () {
    expect(config('edudev.two_factor.enabled'))->toBeTrue(); // set by beforeEach

    config(['edudev.two_factor.enabled' => false]);
    $student = userWithRole(Role::Student->value);

    $this->actingAs($student)->get(route('settings.two-factor'))->assertNotFound();
});

it('defaults to on only when APP_ENV is production', function () {
    $file = file_get_contents(config_path('edudev.php'));

    expect($file)->toContain("env('TWO_FACTOR_ENABLED', env('APP_ENV') === 'production')");
});

it('lets any user set it up with an authenticator code and stores the secret encrypted', function () {
    $student = userWithRole(Role::Student->value);

    Volt::actingAs($student)->test('settings.two-factor')
        ->set('password', 'password')
        ->call('start')
        ->assertHasNoErrors();

    $raw = DB::table('users')->where('id', $student->id)->value('two_factor_secret');
    expect($raw)->not->toBe($student->fresh()->two_factor_secret); // encrypted at rest

    $component = Volt::actingAs($student)->test('settings.two-factor')
        ->set('code', currentCode($student))
        ->call('confirm')
        ->assertHasNoErrors();

    expect($student->fresh()->hasTwoFactorEnabled())->toBeTrue()
        ->and($component->get('freshCodes'))->toHaveCount(8);
});

it('rejects a wrong setup code', function () {
    $student = userWithRole(Role::Student->value);
    app(TwoFactorService::class)->begin($student);

    Volt::actingAs($student)->test('settings.two-factor')
        ->set('code', '000000')
        ->call('confirm')
        ->assertHasErrors('code');

    expect($student->fresh()->hasTwoFactorEnabled())->toBeFalse();
});

it('asks for the code after the password and does not sign in until it is given', function () {
    $student = userWithRole(Role::Student->value);
    enableTwoFactor($student);
    $student->fresh()->forceFill(['two_factor_last_step' => null])->save(); // allow the setup code once more

    Volt::test('auth.login')
        ->set('email', $student->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('two-factor.challenge'));

    $this->assertGuest();

    Volt::test('auth.two-factor-challenge')
        ->set('code', currentCode($student))
        ->call('verify')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($student);
});

it('rejects a wrong code and an already-used code', function () {
    $student = userWithRole(Role::Student->value);
    enableTwoFactor($student);
    $usedCode = currentCode($student); // the code that confirmed setup

    Volt::test('auth.login')->set('email', $student->email)->set('password', 'password')->call('login');

    Volt::test('auth.two-factor-challenge')->set('code', '123456')->call('verify')->assertHasErrors('code');
    Volt::test('auth.two-factor-challenge')->set('code', $usedCode)->call('verify')->assertHasErrors('code');

    $this->assertGuest();
});

it('accepts each recovery code only once', function () {
    $student = userWithRole(Role::Student->value);
    $codes = enableTwoFactor($student);

    Volt::test('auth.login')->set('email', $student->email)->set('password', 'password')->call('login');
    Volt::test('auth.two-factor-challenge')->set('code', $codes[0])->call('verify')->assertHasNoErrors();
    $this->assertAuthenticatedAs($student);

    auth()->logout();

    Volt::test('auth.login')->set('email', $student->email)->set('password', 'password')->call('login');
    Volt::test('auth.two-factor-challenge')->set('code', $codes[0])->call('verify')->assertHasErrors('code');

    expect($student->fresh()->two_factor_recovery_codes)->toHaveCount(7)
        ->and(AuditLog::query()->where('action', 'two_factor.recovery_code_used')->exists())->toBeTrue();
});

it('cannot be reached without a pending sign-in', function () {
    Volt::test('auth.two-factor-challenge')->assertRedirect(route('login'));
});

it('also protects the staff sign-in', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);
    enableTwoFactor($admin);
    $admin->fresh()->forceFill(['two_factor_last_step' => null])->save(); // allow the setup code once more

    Volt::test('admin.auth.login')
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('two-factor.challenge'));

    $this->assertGuest();

    Volt::test('auth.two-factor-challenge')
        ->set('code', currentCode($admin))
        ->call('verify')
        ->assertRedirect(route('admin.dashboard', absolute: false));

    expect(AuditLog::query()->where('action', 'admin.login')->where('actor_id', $admin->id)->exists())->toBeTrue();
});

it('holds staff on the setup screen until they turn it on', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertRedirect(route('settings.two-factor'));

    enableTwoFactor($admin);

    $this->actingAs($admin->fresh())->get(route('admin.dashboard'))->assertOk();
});

it('does not force students to use it', function () {
    $this->actingAs(userWithRole(Role::Student->value))->get(route('dashboard'))->assertOk();
});

it('does not let staff turn off required two-step login', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);
    enableTwoFactor($admin);

    Volt::actingAs($admin->fresh())->test('settings.two-factor')
        ->set('password', 'password')
        ->call('disable')
        ->assertHasErrors('password');

    expect($admin->fresh()->hasTwoFactorEnabled())->toBeTrue();
});

it('lets students turn it off with their password', function () {
    $student = userWithRole(Role::Student->value);
    enableTwoFactor($student);

    Volt::actingAs($student->fresh())->test('settings.two-factor')
        ->set('password', 'password')
        ->call('disable')
        ->assertHasNoErrors();

    expect($student->fresh()->hasTwoFactorEnabled())->toBeFalse();
});

it('lets an admin reset it for a user, with an audit record', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $student = userWithRole(Role::Student->value);
    enableTwoFactor($student);

    app(AccountAdministration::class)->resetTwoFactor($pa, $student);

    expect($student->fresh()->hasTwoFactorEnabled())->toBeFalse()
        ->and(AuditLog::query()->where('action', 'two_factor.reset_by_admin')->where('subject_id', $student->id)->exists())->toBeTrue();
});

it('never exposes the secret or recovery codes in serialised user data', function () {
    $student = userWithRole(Role::Student->value);
    enableTwoFactor($student);

    $json = $student->fresh()->toJson();

    expect($json)->not->toContain('two_factor_secret')
        ->and($json)->not->toContain('two_factor_recovery_codes');
});
