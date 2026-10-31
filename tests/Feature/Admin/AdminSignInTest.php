<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\Volt\Volt;

/*
| The admin panel has its own sign-in page, separate from the user /login.
*/

it('shows a separate staff sign-in page', function () {
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertSee('Staff sign-in')
        ->assertSee('noindex', false);

    expect(route('admin.login'))->not->toBe(route('login'));
});

it('sends signed-out visitors of the admin area to the staff sign-in page', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.branding'))->assertRedirect(route('admin.login'));
});

it('sends signed-out visitors of the user area to the user sign-in page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('lets staff sign in through the staff page and records it', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);

    Volt::test('admin.auth.login')
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.dashboard', absolute: false));

    $this->assertAuthenticatedAs($admin);
    expect(AuditLog::query()->where('action', 'admin.login')->where('actor_id', $admin->id)->exists())->toBeTrue();
});

it('rejects student credentials on the staff page with a generic error', function () {
    $student = userWithRole(Role::Student->value);

    Volt::test('admin.auth.login')
        ->set('email', $student->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email' => __('auth.failed')]);

    $this->assertGuest();
});

it('rejects staff on the user sign-in page', function () {
    $admin = userWithRole(Role::SuperAdmin->value);

    Volt::test('auth.login')
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('still lets students sign in on the user page', function () {
    $student = userWithRole(Role::Student->value);

    Volt::test('auth.login')
        ->set('email', $student->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($student);
    expect($student->fresh()->last_login_at)->not->toBeNull();
});

it('rejects suspended staff on the staff page', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);
    $admin->forceFill(['status' => User::STATUS_SUSPENDED])->save();

    Volt::test('admin.auth.login')
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('locks the staff page after five failed attempts', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);

    foreach (range(1, 5) as $i) {
        Volt::test('admin.auth.login')->set('email', $admin->email)->set('password', 'wrong')->call('login');
    }

    Volt::test('admin.auth.login')
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('sends staff back to the staff sign-in page on logout', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);

    $this->actingAs($admin)->post(route('logout'))->assertRedirect(route('admin.login'));
});

it('sends signed-in staff who open the staff sign-in page to the admin dashboard', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);

    $this->actingAs($admin)->get(route('admin.login'))->assertRedirect(route('admin.dashboard'));
});
