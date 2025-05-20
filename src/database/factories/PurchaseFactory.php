<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id ?? null,
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'address_id' => Address::factory(),
            'purchase_price' => function (array $attributes) {
                $item = Item::find($attributes['item_id']);
                return $item ? $item->price : null;
            },
            'payment_method' => $this->faker->randomElement(['konbini', 'card']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
