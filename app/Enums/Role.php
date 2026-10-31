<?php

namespace App\Enums;

/**
 * Platform-level roles. Institution-specific roles (e.g. a teacher inside one
 * school) will be modelled through institution memberships in a later phase,
 * so that permissions are always scoped to a tenant.
 */
enum Role: string
{
    case SuperAdmin = 'super_admin';
    case PlatformAdmin = 'platform_admin';
    case ContentManager = 'content_manager';
    case SupportAgent = 'support_agent';
    case Student = 'student';
    case Guardian = 'guardian';
    case Teacher = 'teacher';
    case Tutor = 'tutor';
    case SchoolAdmin = 'school_admin';
    case CbtCentreAdmin = 'cbt_centre_admin';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::PlatformAdmin => 'Platform Admin',
            self::ContentManager => 'Content Manager',
            self::SupportAgent => 'Support Agent',
            self::Student => 'Student',
            self::Guardian => 'Parent / Guardian',
            self::Teacher => 'Teacher',
            self::Tutor => 'Tutor',
            self::SchoolAdmin => 'School Administrator',
            self::CbtCentreAdmin => 'CBT Centre Administrator',
        };
    }

    /** Staff roles that can enter the /admin area. */
    public static function staff(): array
    {
        return [self::SuperAdmin, self::PlatformAdmin, self::ContentManager, self::SupportAgent];
    }
}
