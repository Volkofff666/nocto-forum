<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'username'          => fake()->unique()->userName(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role'              => 'user',
            'banned_at'         => null,
            'ban_reason'        => null,
        ];
    }

    public function moderator(): static
    {
        return $this->state(['role' => 'moderator']);
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function banned(string $reason = 'Нарушение правил'): static
    {
        return $this->state([
            'banned_at'  => now(),
            'ban_reason' => $reason,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
