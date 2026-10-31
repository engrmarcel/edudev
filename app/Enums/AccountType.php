<?php

namespace App\Enums;

/**
 * Account types a person can choose during self-service onboarding.
 * Staff roles are deliberately NOT here: they can only be granted by an
 * administrator, never self-selected.
 */
enum AccountType: string
{
    case Student = 'student';
    case Guardian = 'guardian';
    case Teacher = 'teacher';
    case Tutor = 'tutor';
    case School = 'school';
    case CbtCentre = 'cbt_centre';

    public function role(): Role
    {
        return match ($this) {
            self::Student => Role::Student,
            self::Guardian => Role::Guardian,
            self::Teacher => Role::Teacher,
            self::Tutor => Role::Tutor,
            self::School => Role::SchoolAdmin,
            self::CbtCentre => Role::CbtCentreAdmin,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Guardian => 'Parent or guardian',
            self::Teacher => 'Teacher',
            self::Tutor => 'Private tutor',
            self::School => 'School administrator',
            self::CbtCentre => 'CBT centre operator',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Student => 'Learn with the AI Tutor, practise questions and take mock exams.',
            self::Guardian => 'Follow and sponsor a child\'s learning.',
            self::Teacher => 'Set work and see how your classes are doing.',
            self::Tutor => 'Support your own students with practice and progress tracking.',
            self::School => 'Set up your school, invite teachers and enrol students.',
            self::CbtCentre => 'Run timed mock examinations for candidates.',
        };
    }

    /** True when the account represents an organisation. */
    public function isOrganisation(): bool
    {
        return in_array($this, [self::School, self::CbtCentre], true);
    }

    /** Self-service roles, i.e. roles onboarding is allowed to assign. */
    public static function selfServiceRoles(): array
    {
        return array_map(fn (self $t) => $t->role()->value, self::cases());
    }
}
