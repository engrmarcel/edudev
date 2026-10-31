<?php

namespace App\Services\Accounts;

use App\Enums\AccountType;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LogicException;
use Spatie\Permission\Models\Role as SpatieRole;

class OnboardingService
{
    public function __construct(private readonly AuditLogger $audit) {}

    /**
     * Records the policies a user accepted at registration (current versions).
     */
    public function recordPolicyAcceptance(User $user, ?Request $request = null): void
    {
        foreach (config('edudev.policies') as $policy => $version) {
            $user->policyAcceptances()->firstOrCreate(
                ['policy' => $policy, 'version' => $version],
                [
                    'accepted_at' => now(),
                    'ip_address' => $request?->ip(),
                    'user_agent' => $request ? substr((string) $request->userAgent(), 0, 512) : null,
                ],
            );
        }
    }

    /**
     * Saves the profile for the chosen account type and gives the user the
     * matching role. Only self-service roles can ever be assigned here.
     *
     * @param  array<string, mixed>  $data  validated profile fields
     */
    public function complete(User $user, AccountType $type, array $data): void
    {
        if ($user->isStaff()) {
            throw new LogicException('Staff accounts do not go through self-service onboarding.');
        }

        // Keep only the fields relevant to this account type (data minimisation).
        $allowed = match (true) {
            $type === AccountType::Student => ['class_level', 'exam_goals'],
            $type->isOrganisation() => ['organisation_name', 'state', 'phone'],
            $type === AccountType::Teacher => ['organisation_name', 'state'],
            default => ['state'],
        };
        $fields = array_intersect_key($data, array_flip($allowed));
        $empty = array_fill_keys(['phone', 'class_level', 'exam_goals', 'organisation_name', 'state'], null);

        DB::transaction(function () use ($user, $type, $fields, $empty) {
            $profile = $user->profile()->firstOrNew();
            $profile->fill([...$empty, ...$fields, 'account_type' => $type]);
            $profile->onboarded_at = now();
            $profile->save();

            $keep = $user->roles->pluck('name')->diff(AccountType::selfServiceRoles())->all();
            $user->syncRoles([...$keep, SpatieRole::findOrCreate($type->role()->value, 'web')->name]);
        });

        $user->unsetRelation('profile');

        $this->audit->record('account.onboarded', $user, context: ['account_type' => $type->value], actor: $user);
    }
}
