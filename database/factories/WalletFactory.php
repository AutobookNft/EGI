<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'collection_id' => Collection::factory(),
            'platform_role' => $this->faker->randomElement(['Frangette', 'Mediator', 'Creator']),
            'wallet' => $this->faker->unique()->regexify('[a-zA-Z0-9]{42}'),
            'royalty_mint' => $this->faker->randomFloat(2, 0, 50),
            'royalty_rebind' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
}
