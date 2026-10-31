<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Accounts\AccountAdministration;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Volt;

function admin(): AccountAdministration
{
    return app(AccountAdministration::class);
}

it('lets a platform admin search users', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    userWithRole(Role::Student->value, ['name' => 'Chidi Okeke']);
    userWithRole(Role::Student->value, ['name' => 'Bisi Adeyemi']);

    $this->actingAs($pa)->get(route('admin.users.index', ['search' => 'Chidi']))
        ->assertOk()
        ->assertSee('Chidi Okeke')
        ->assertDontSee('Bisi Adeyemi');
});

it('blocks users without users.manage from user management', function () {
    $cm = userWithRole(Role::ContentManager->value);
    $student = userWithRole(Role::Student->value);

    $this->actingAs($cm)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($cm)->get(route('admin.users.show', $student))->assertForbidden();
    $this->actingAs($cm)->get(route('admin.deletion-requests'))->assertForbidden();
});

it('suspends a user with a reason, ends their sessions and audits it', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $student = userWithRole(Role::Student->value);
    DB::table('sessions')->insert(['id' => 'abc', 'user_id' => $student->id, 'payload' => '', 'last_activity' => time()]);

    admin()->suspend($pa, $student, 'Sharing exam access codes');

    expect($student->fresh()->isSuspended())->toBeTrue()
        ->and($student->fresh()->suspension_reason)->toBe('Sharing exam access codes')
        ->and(DB::table('sessions')->where('user_id', $student->id)->count())->toBe(0)
        ->and(AuditLog::query()->where('action', 'user.suspended')->where('subject_id', $student->id)->exists())->toBeTrue();

    admin()->reactivate($pa, $student);
    expect($student->fresh()->isSuspended())->toBeFalse();
});

it('requires a suspension reason', function () {
    admin()->suspend(userWithRole(Role::PlatformAdmin->value), userWithRole(Role::Student->value), '  ');
})->throws(ValidationException::class);

it('works through the admin screen', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $student = userWithRole(Role::Student->value);

    Volt::actingAs($pa)->test('admin.users.show', ['user' => $student])
        ->set('reason', 'Abusive messages')
        ->call('suspend')
        ->assertHasNoErrors();

    expect($student->fresh()->isSuspended())->toBeTrue();
});

it('prevents staff from acting on themselves', function () {
    $sa = userWithRole(Role::SuperAdmin->value);
    userWithRole(Role::SuperAdmin->value); // another super admin exists

    expect(fn () => admin()->suspend($sa, $sa, 'test'))->toThrow(AuthorizationException::class);
    expect(fn () => admin()->syncRoles($sa, $sa, []))->toThrow(AuthorizationException::class);
});

it('stops platform admins from changing roles at all', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $student = userWithRole(Role::Student->value);

    admin()->syncRoles($pa, $student, [Role::PlatformAdmin->value]);
})->throws(AuthorizationException::class);

it('stops non-super-admins from touching super admin accounts', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $sa = userWithRole(Role::SuperAdmin->value);
    userWithRole(Role::SuperAdmin->value);

    admin()->suspend($pa, $sa, 'takeover attempt');
})->throws(AuthorizationException::class);

it('lets a super admin change roles and audits the change', function () {
    $sa = userWithRole(Role::SuperAdmin->value);
    $student = userWithRole(Role::Student->value);

    admin()->syncRoles($sa, $student, [Role::Student->value, Role::ContentManager->value, 'not_a_role']);

    $roles = $student->fresh()->roles->pluck('name')->sort()->values()->all();
    expect($roles)->toBe([Role::ContentManager->value, Role::Student->value]);

    $log = AuditLog::query()->where('action', 'user.roles_changed')->first();
    expect($log->changes['roles']['to'])->toBe($roles);
});

it('never removes or suspends the last active super admin', function () {
    $sa = userWithRole(Role::SuperAdmin->value);
    $other = userWithRole(Role::SuperAdmin->value);

    // Two super admins: demoting one is fine.
    admin()->syncRoles($sa, $other, []);

    // Now $sa is the last one; a newly promoted super admin cannot remove them.
    admin()->syncRoles($sa, $other, [Role::SuperAdmin->value]);
    $other->forceFill(['status' => User::STATUS_SUSPENDED])->save();

    expect(fn () => admin()->suspend($other->fresh(), $sa, 'x'))->toThrow(Exception::class);
    expect($sa->fresh()->isSuspended())->toBeFalse();
});

it('does not show private learning content on the user page', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $student = userWithRole(Role::Student->value);

    $this->actingAs($pa)->get(route('admin.users.show', $student))
        ->assertOk()
        ->assertSee($student->email)
        ->assertDontSee('password');
});
