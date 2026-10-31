<?php

use App\Enums\Permission;
use App\Enums\Role;
use Tests\TestCase;

uses(TestCase::class);

it('formats money from integer minor units', function () {
    expect(money_format_minor(150000))->toContain('1,500.00')
        ->and(money_format_minor(5))->toContain('0.05');
});

it('gives super admins every permission and limits other staff', function () {
    $matrix = Permission::matrix();

    expect($matrix[Role::SuperAdmin->value])->toHaveCount(count(Permission::cases()))
        ->and($matrix[Role::PlatformAdmin->value])->not->toContain(Permission::ManageRoles)
        ->and($matrix[Role::ContentManager->value])->not->toContain(Permission::ManageBranding)
        ->and($matrix)->not->toHaveKey(Role::Student->value);
});

it('marks only staff roles as staff', function () {
    expect(Role::staff())->not->toContain(Role::Student)
        ->and(Role::staff())->not->toContain(Role::Teacher);
});
