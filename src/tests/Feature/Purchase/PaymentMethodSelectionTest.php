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

        $response = $this->get("/purchase/{$item->id}"); 
        $response->assertSee("選択してください"); 

        $responsePostKonbini = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini',
        ]);

        $responsePostKonbini->assertRedirect("/purchase/{$item->id}");
        $followedResponse = $this->followRedirects($responsePostKonbini);
        $followedResponse->assertSee('コンビニ支払い');

        $responsePostCard = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'card',
        ]);

        $responsePostCard->assertRedirect("/purchase/{$item->id}");
        $followedResponseCard = $this->followRedirects($responsePostCard);
        $followedResponseCard->assertSee('カード支払い');
    }
}