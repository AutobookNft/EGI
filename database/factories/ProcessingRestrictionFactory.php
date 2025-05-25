<?php

namespace Database\Factories;

use App\Models\ProcessingRestriction;
use App\Models\User;
use App\Enums\Gdpr\ProcessingRestrictionType;
use App\Enums\Gdpr\ProcessingRestrictionReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessingRestriction>
 */
class ProcessingRestrictionFactory extends Factory
{
    protected $model = ProcessingRestriction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ProcessingRestrictionType::cases();
        $reasons = ProcessingRestrictionReason::cases();

        $dataCategories = ['personal_info', 'financial_data', 'behavioral_data', 'location_data'];

        return [
            'user_id' => User::factory(),
            'restriction_type' => $this->faker->randomElement($types)->value,
            'reason' => $this->faker->randomElement($reasons)->value,
            'details' => $this->faker->paragraph(),
            'affected_data_categories' => $this->faker->optional(0.7)->passthrough(
                $this->faker->randomElements($dataCategories, $this->faker->numberBetween(1, 3))
            ),
            'is_active' => true,
            'lifted_at' => null,
            'lifted_by' => null,
            'lift_reason' => null,
        ];
    }

    /**
     * Indicate that the restriction is lifted.
     */
    public function lifted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'lifted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'lifted_by' => 'admin@florenceegi.com',
            'lift_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the restriction is for all processing.
     */
    public function allProcessing(): static
    {
        return $this->state(fn (array $attributes) => [
            'restriction_type' => ProcessingRestrictionType::ALL->value,
            'affected_data_categories' => null,
        ]);
    }
}
