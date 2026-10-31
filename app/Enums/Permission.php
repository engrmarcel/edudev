<?php

namespace App\Enums;

/**
 * Fine-grained permissions. Code checks permissions (not role names) so that
 * roles can be re-shaped later without touching business logic.
 */
enum Permission: string
{
    case AccessAdmin = 'admin.access';
    case ManageBranding = 'settings.branding.manage';
    case ManageSettings = 'settings.system.manage';
    case ViewAuditLogs = 'audit.view';
    case ManageUsers = 'users.manage';
    case ManageRoles = 'roles.manage';
    case ManageCurriculum = 'curriculum.manage';
    case ManageQuestions = 'questions.manage';
    case ManagePlans = 'billing.plans.manage';
    case ViewRevenue = 'billing.revenue.view';
    case ManageAiSettings = 'ai.settings.manage';
    case ViewAiUsage = 'ai.usage.view';
    case ManageSupport = 'support.manage';
    case ViewSystemHealth = 'system.health.view';

    /** @return array<string, list<self>> role value => permissions */
    public static function matrix(): array
    {
        $all = self::cases();

        return [
            Role::SuperAdmin->value => $all,
            Role::PlatformAdmin->value => array_values(array_filter($all, fn (self $p) => ! in_array($p, [self::ManageRoles, self::ManageAiSettings], true))),
            Role::ContentManager->value => [self::AccessAdmin, self::ManageCurriculum, self::ManageQuestions],
            Role::SupportAgent->value => [self::AccessAdmin, self::ManageSupport],
        ];
    }
}
