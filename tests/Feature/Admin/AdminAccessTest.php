<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Services\Audit\AuditLogger;
use Livewire\Volt\Volt;

it('blocks students from the admin area', function () {
    $student = userWithRole(Role::Student->value);

    $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($student)->get(route('admin.branding'))->assertForbidden();
    $this->actingAs($student)->get(route('admin.audit'))->assertForbidden();
});

it('shows the admin overview to staff', function () {
    $admin = userWithRole(Role::PlatformAdmin->value);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Platform overview');
});

it('applies least privilege: content managers cannot change branding', function () {
    $cm = userWithRole(Role::ContentManager->value);

    $this->actingAs($cm)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($cm)->get(route('admin.branding'))->assertForbidden();
    $this->actingAs($cm)->get(route('admin.audit'))->assertForbidden();
});

it('refuses the branding save action for users without permission', function () {
    $student = userWithRole(Role::Student->value);

    Volt::actingAs($student)->test('admin.branding')->assertForbidden();
});

it('lets a super admin view the audit log', function () {
    $admin = userWithRole(Role::SuperAdmin->value);
    app(AuditLogger::class)->record('test.event', actor: $admin);

    $this->actingAs($admin)->get(route('admin.audit'))
        ->assertOk()
        ->assertSee('test.event');
});

it('keeps audit logs append-only', function () {
    $log = app(AuditLogger::class)->record('test.event');

    expect(fn () => $log->update(['action' => 'changed']))->toThrow(LogicException::class);
    expect(fn () => $log->delete())->toThrow(LogicException::class);
    expect(AuditLog::query()->count())->toBe(1);
});
