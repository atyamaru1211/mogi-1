<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use App\Models\Profile;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    // ID:11 支払い方法選択機能
    // 小計画面で変更が即時反映される
    public function testPaymentMethodIsReflectedOnSubtotalScreen()
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $buyer->profile()->create([
            'name' => 'テストユーザー名',
            'postal_code' => '123-4567',
            'address' => '東京都墨田区押上',
            'building' => 'スカイツリービル1F',
        ]);

        $seller = User::factory()->create();

        $this->seed(ItemsSeeder::class);
        $item = Item::first();
        $item->update(['user_id' => $seller->id]);

        $address = Address::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都墨田区押上',
            'building' => 'スカイツリービル1F',
        ]);

        $response = $this->get("/purchase/{$item->id}"); 

        $response->assertSee("選択してください");

        $responsePostKonbini = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini',
            'address_id' => $address->id, 
            'purchase_price' => $item->price, 
            'address_existence' => 'exists',
        ]);

        $responsePostKonbini->assertRedirectContains('https://checkout.stripe.com');

        $this->withSession(['payment_method_type' => 'konbini'])
             ->actingAs($buyer)
             ->get("/item/{$item->id}/purchased");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'コンビニ払い',
        ]);

    }

}