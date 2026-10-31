<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds REFERENCE data only (safe for production).
     *
     * No demo users or passwords are created here. Create the first
     * administrator with: php artisan edudev:create-admin
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
