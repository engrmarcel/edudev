<?php

use App\Enums\Role;
use App\Models\AccountDeletionRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Accounts\AccountDeletionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Volt;

function deletions(): AccountDeletionService
{
    return app(AccountDeletionService::class);
}

it('creates only one pending request per user', function () {
    $student = userWithRole(Role::Student->value);

    deletions()->request($student, 'Leaving');
    deletions()->request($student, 'Again');

    expect($student->deletionRequests()->count())->toBe(1);
});

it('lets the user cancel their own pending request', function () {
    $student = userWithRole(Role::Student->value);
    deletions()->request($student);

    Volt::actingAs($student)->test('settings.delete-user-form')->call('cancelRequest');

    expect($student->deletionRequests()->first()->status)->toBe(AccountDeletionRequest::CANCELLED);
});

it('deletes the account when staff complete the request, keeping a record', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $student = userWithRole(Role::Student->value);
    $request = deletions()->request($student, 'Leaving');

    Volt::actingAs($pa)->test('admin.deletion-requests')->call('complete', $request->id);

    expect(User::query()->find($student->id))->toBeNull();

    $request->refresh();
    expect($request->status)->toBe(AccountDeletionRequest::COMPLETED)
        ->and($request->user_id)->toBeNull()
        ->and($request->reviewed_by)->toBe($pa->id)
        ->and(AuditLog::query()->where('action', 'account.deleted')->exists())->toBeTrue();
});

it('requires a note to reject a request', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $request = deletions()->request(userWithRole(Role::Student->value));

    deletions()->reject($pa, $request, '');
})->throws(ValidationException::class);

it('cannot process the same request twice', function () {
    $pa = userWithRole(Role::PlatformAdmin->value);
    $request = deletions()->request(userWithRole(Role::Student->value));

    deletions()->reject($pa, $request, 'Outstanding school licence');
    deletions()->complete($pa, $request->fresh());
})->throws(ValidationException::class);

it('stops users without permission from completing requests', function () {
    $student = userWithRole(Role::Student->value);
    $victim = userWithRole(Role::Student->value);
    $request = deletions()->request($victim);

    deletions()->complete($student, $request);
})->throws(AuthorizationException::class);

it('refuses to delete staff accounts through this workflow', function () {
    $sa = userWithRole(Role::SuperAdmin->value);
    $staff = userWithRole(Role::SupportAgent->value);
    $request = deletions()->request($staff);

    deletions()->complete($sa, $request);
})->throws(ValidationException::class);
