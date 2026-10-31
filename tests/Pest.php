<?php

use App\Enums\AccountType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
| Feature tests run against the MySQL database "edudev_testing" (see
| phpunit.xml) and are reset between tests with RefreshDatabase.
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/** Seed roles/permissions and create a user that holds the given role. */
function userWithRole(string $role, array $attributes = []): User
{
    test()->seed(RolesAndPermissionsSeeder::class);

    // Self-service roles get a completed onboarding profile, as real users would.
    $type = collect(AccountType::cases())->first(fn (AccountType $t) => $t->role()->value === $role);

    if ($type) {
        return User::factory()->onboarded($type)->create($attributes);
    }

    $user = User::factory()->create($attributes);
    $user->assignRole($role);

    return $user;
}
