<?php

namespace Database\Factories;

use App\Models\Epp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Epp>
 */
class EppFactory extends Factory {
    protected $model = Epp::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['ARF', 'APR', 'BPE']),
            'description' => $this->faker->paragraph(),
            'image_path' => null,
            'status' => 'active',
            'total_funds' => $this->faker->randomFloat(2, 0, 1000),
            'target_funds' => $this->faker->randomFloat(2, 1000, 50000),
            'manager_id' => null,
        ];
    }

    /**
     * Stato per EPP attivo
     */
    public function active() {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }
}
