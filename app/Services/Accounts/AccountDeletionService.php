<?php

namespace App\Services\Accounts;

use App\Enums\Permission;
use App\Models\AccountDeletionRequest;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Deletion is a reviewed request, not an instant action, so that records
 * which must be retained (payments, institution exam results, audit) can be
 * handled correctly before the account is removed.
 */
class AccountDeletionService
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function request(User $user, ?string $reason = null): AccountDeletionRequest
    {
        if ($existing = $user->deletionRequests()->pending()->first()) {
            return $existing;
        }

        $request = $user->deletionRequests()->create([
            'user_label' => $user->name.' <'.$user->email.'>',
            'reason' => $reason ? mb_substr(trim($reason), 0, 2000) : null,
        ]);

        $this->audit->record('account.deletion_requested', $user, actor: $user);

        return $request;
    }

    public function cancel(User $user, AccountDeletionRequest $request): void
    {
        if ($request->user_id !== $user->id || ! $request->isPending()) {
            throw new AuthorizationException;
        }

        $request->forceFill(['status' => AccountDeletionRequest::CANCELLED])->save();
        $this->audit->record('account.deletion_cancelled', $user, actor: $user);
    }

    public function complete(User $actor, AccountDeletionRequest $request, ?string $note = null): void
    {
        $this->authorizeReview($actor, $request);
        $target = $request->user;

        if ($target && $target->isStaff()) {
            throw ValidationException::withMessages(['request' => __('Remove staff roles before deleting a staff account.')]);
        }

        DB::transaction(function () use ($actor, $request, $target, $note) {
            $request->forceFill([
                'status' => AccountDeletionRequest::COMPLETED,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_note' => $note,
            ])->save();

            if ($target) {
                DB::table('sessions')->where('user_id', $target->id)->delete();
                $target->delete();
            }
        });

        $this->audit->record('account.deleted', context: ['request_id' => $request->id, 'user' => $request->user_label], actor: $actor);
    }

    public function reject(User $actor, AccountDeletionRequest $request, string $note): void
    {
        $this->authorizeReview($actor, $request);

        if (trim($note) === '') {
            throw ValidationException::withMessages(['note' => __('Explain why the request is rejected.')]);
        }

        $request->forceFill([
            'status' => AccountDeletionRequest::REJECTED,
            'reviewed_by' => $actor->id,
            'reviewed_at' => now(),
            'review_note' => mb_substr($note, 0, 1000),
        ])->save();

        $this->audit->record('account.deletion_rejected', $request->user, context: ['request_id' => $request->id], actor: $actor);
    }

    private function authorizeReview(User $actor, AccountDeletionRequest $request): void
    {
        if (! Gate::forUser($actor)->allows(Permission::ManageUsers->value)) {
            throw new AuthorizationException;
        }
        if ($request->user_id === $actor->id) {
            throw new AuthorizationException(__('You cannot review your own deletion request.'));
        }
        if (! $request->isPending()) {
            throw ValidationException::withMessages(['request' => __('This request has already been handled.')]);
        }
    }
}
