<?php

namespace App\Services\Accounts;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Auth\TwoFactorService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Privileged account actions. Every method re-checks permissions itself so
 * it is safe no matter which screen or API calls it.
 *
 * Escalation rules:
 *  - nobody can suspend themselves or change their own roles;
 *  - only a super admin can act on a super admin, or grant super_admin;
 *  - the last active super admin can never be suspended or demoted.
 */
class AccountAdministration
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function suspend(User $actor, User $target, string $reason): void
    {
        $this->authorize($actor, Permission::ManageUsers, $target);
        $this->guardLastSuperAdmin($target);

        $reason = trim($reason);
        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => __('Give a reason for the suspension.')]);
        }

        DB::transaction(function () use ($target, $reason) {
            $target->forceFill([
                'status' => User::STATUS_SUSPENDED,
                'suspended_at' => now(),
                'suspension_reason' => mb_substr($reason, 0, 500),
            ])->save();

            // End any open sessions immediately (database session driver).
            DB::table('sessions')->where('user_id', $target->id)->delete();
        });

        $this->audit->record('user.suspended', $target, context: ['reason' => $reason], actor: $actor);
    }

    public function reactivate(User $actor, User $target): void
    {
        $this->authorize($actor, Permission::ManageUsers, $target);

        $target->forceFill([
            'status' => User::STATUS_ACTIVE,
            'suspended_at' => null,
            'suspension_reason' => null,
        ])->save();

        $this->audit->record('user.reactivated', $target, actor: $actor);
    }

    /**
     * For users who lost their phone AND their recovery codes. Verify the
     * person's identity out of band before using this.
     */
    public function resetTwoFactor(User $actor, User $target): void
    {
        $this->authorize($actor, Permission::ManageUsers, $target);

        app(TwoFactorService::class)->disable($target, $actor);
        DB::table('sessions')->where('user_id', $target->id)->delete();
    }

    /**
     * @param  list<string>  $roles  role names the target should end up with
     */
    public function syncRoles(User $actor, User $target, array $roles): void
    {
        $this->authorize($actor, Permission::ManageRoles, $target);

        $valid = array_map(fn (Role $r) => $r->value, Role::cases());
        $roles = array_values(array_unique(array_intersect($roles, $valid)));

        $before = $target->roles->pluck('name')->sort()->values()->all();
        $grantsSuper = in_array(Role::SuperAdmin->value, $roles, true);
        $removesSuper = in_array(Role::SuperAdmin->value, $before, true) && ! $grantsSuper;

        if (($grantsSuper || $removesSuper) && ! $actor->hasRole(Role::SuperAdmin->value)) {
            throw new AuthorizationException(__('Only a super admin can grant or remove the super admin role.'));
        }
        if ($removesSuper) {
            $this->guardLastSuperAdmin($target);
        }

        $target->syncRoles($roles);
        $after = $target->fresh()->roles->pluck('name')->sort()->values()->all();

        if ($before !== $after) {
            $this->audit->record('user.roles_changed', $target, changes: ['roles' => ['from' => $before, 'to' => $after]], actor: $actor);
        }
    }

    private function authorize(User $actor, Permission $permission, User $target): void
    {
        if (! Gate::forUser($actor)->allows($permission->value)) {
            throw new AuthorizationException;
        }
        if ($actor->is($target)) {
            throw new AuthorizationException(__('You cannot change your own account here.'));
        }
        if ($target->hasRole(Role::SuperAdmin->value) && ! $actor->hasRole(Role::SuperAdmin->value)) {
            throw new AuthorizationException(__('Only a super admin can change a super admin account.'));
        }
    }

    private function guardLastSuperAdmin(User $target): void
    {
        if (! $target->hasRole(Role::SuperAdmin->value)) {
            return;
        }

        $others = User::role(Role::SuperAdmin->value)
            ->where('users.id', '!=', $target->id)
            ->where('status', User::STATUS_ACTIVE)
            ->exists();

        if (! $others) {
            throw ValidationException::withMessages(['user' => __('This is the last active super admin and cannot be suspended or demoted.')]);
        }
    }
}
