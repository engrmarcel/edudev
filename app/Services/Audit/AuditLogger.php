<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Records sensitive actions. Callers must not pass secrets or private
 * student content in $changes or $context.
 */
class AuditLogger
{
    public function record(
        string $action,
        ?Model $subject = null,
        array $changes = [],
        array $context = [],
        ?User $actor = null,
    ): AuditLog {
        $actor ??= Auth::user();
        $request = app()->runningInConsole() && ! app()->runningUnitTests() ? null : request();

        return AuditLog::query()->create([
            'actor_id' => $actor?->getKey(),
            'actor_label' => $actor ? $actor->name.' <'.$actor->email.'>' : 'system',
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'changes' => $changes ?: null,
            'context' => $context ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 512) : null,
            'request_id' => $request?->attributes->get('request_id'),
        ]);
    }
}
