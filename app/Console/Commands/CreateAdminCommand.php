<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Creates (or promotes) a Super Admin. Passwords are typed interactively
 * and never stored in code, seeders, or shell history.
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'edudev:create-admin
        {email? : Email address of the administrator}
        {--name= : Display name}
        {--role=super_admin : super_admin or platform_admin}';

    protected $description = 'Create or promote a platform administrator account';

    public function handle(AuditLogger $audit): int
    {
        $roleName = (string) $this->option('role');
        if (! in_array($roleName, [Role::SuperAdmin->value, Role::PlatformAdmin->value], true)) {
            $this->error('Role must be super_admin or platform_admin.');

            return self::FAILURE;
        }

        $this->callSilently('db:seed', ['--class' => RolesAndPermissionsSeeder::class, '--force' => true]);

        $email = strtolower(trim((string) ($this->argument('email') ?: text('Admin email address', required: true))));
        $user = User::query()->where('email', $email)->first();

        if ($user) {
            $user->assignRole($roleName);
            $audit->record('admin.role_granted', $user, context: ['role' => $roleName, 'via' => 'cli']);
            $this->info("Existing user {$email} now has the {$roleName} role.");

            return self::SUCCESS;
        }

        $name = (string) ($this->option('name') ?: text('Full name', required: true));
        $plain = password('Password (min. 12 characters, mixed case, numbers, symbols)', required: true);

        $validator = Validator::make(
            ['email' => $email, 'name' => $name, 'password' => $plain],
            [
                'email' => ['required', 'email', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = DB::transaction(function () use ($email, $name, $plain, $roleName) {
            $user = User::query()->create(['name' => $name, 'email' => $email, 'password' => $plain]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->assignRole($roleName);

            return $user;
        });

        $audit->record('admin.created', $user, context: ['role' => $roleName, 'via' => 'cli']);
        $this->info("Administrator {$email} created with role {$roleName}.");

        return self::SUCCESS;
    }
}
