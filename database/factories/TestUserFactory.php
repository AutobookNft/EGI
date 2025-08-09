<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory semplificato per test - senza gestione ruoli automatica
 */
class TestUserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'usertype' => 'user', // Default: user normale
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'wallet' => Str::random(42),
            'wallet_balance' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }

    /**
     * Stato per creator
     */
    public function creator() {
        return $this->state(function (array $attributes) {
            return [
                'usertype' => 'creator',
            ];
        });
    }
}
