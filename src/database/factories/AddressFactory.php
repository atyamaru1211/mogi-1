<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Address;
use App\Models\User;
use App\Models\Item;

class AddressFactory extends Factory
{
    protected $model = Address::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $itemId = Item::inRandomOrder()->first()->id ?? null;

        return [
            'user_id' => User::factory(),
            'item_id' => $itemId,
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
