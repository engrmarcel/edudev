<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * A user who has finished onboarding as the given account type
     * (profile + matching role). Roles must already be seeded or are created.
     */
    public function onboarded(AccountType $type = AccountType::Student): static
    {
        return $this->afterCreating(function (User $user) use ($type) {
            $profile = $user->profile()->make(['account_type' => $type]);
            $profile->onboarded_at = now();
            $profile->save();
            $user->assignRole(Role::findOrCreate($type->role()->value, 'web'));
        });
    }
}
