<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use App\Models\Profile;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PurchaseItemTest extends TestCase
{
    use RefreshDatabase;

    // ID:10 商品購入機能
    // 「購入する」ボタンを押下するとstripeの購入処理画面に遷移し、購入処理後、商品詳細画面に遷移
    public function testUserCanPurchaseItem()
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
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $purchasePrice = $item->price;
        $paymentMethod = 'card';

        $response = $this->post("/purchase/{$item->id}", [
            'address_id' => $address->id,
            'payment_method' => $paymentMethod,
            'purchase_price' => $purchasePrice,
            'address_existence' => 'exists',
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com');

        $this->withSession([
            'payment_method_type' => $paymentMethod,
        ]);

        $responseAfterStripe = $this->actingAs($buyer)->get("/item/{$item->id}/purchased");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'address_id' => $address->id,
            'purchase_price' => $purchasePrice,
            'payment_method' => $paymentMethod === 'konbini' ? 'コンビニ払い' : 'カード払い',
        ]);
    }

    // 購入した商品は商品一覧画面にて「Sold」と表示される
    public function testPurchasedItemShowsSoldOnIndexPage()
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
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $purchasePrice = $item->price;
        $paymentMethod = 'card';

        $response = $this->post("/purchase/{$item->id}", [
            'address_id' => $address->id,
            'payment_method' => $paymentMethod,
            'purchase_price' => $purchasePrice,
            'address_existence' => 'exists',
        ]);

        $this->withSession([
            'payment_method_type' => $paymentMethod,
        ])->actingAs($buyer)->get("/item/{$item->id}/purchased");

        $response = $this->get('/');
        $response->assertSeeText($item->name);
        $response->assertSee('storage/' . $item->image_path);
        $response->assertSee('Sold');
    }

    // 「プロフィール/購入した商品一覧」に追加されている
    public function testPurchasedItemAppearsInBuyerPurchasesList()
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
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $purchasePrice = $item->price;
        $paymentMethod = 'card';

        $response = $this->post("/purchase/{$item->id}", [
            'address_id' => $address->id,
            'payment_method' => $paymentMethod,
            'purchase_price' => $purchasePrice,
            'address_existence' => 'exists',
        ]);

        $this->withSession([
            'payment_method_type' => $paymentMethod,
        ])->actingAs($buyer)->get("/item/{$item->id}/purchased");

        $response = $this->actingAs($buyer)->get('/mypage?tab=buy');
        $response->assertSeeText($item->name);
        $response->assertSee('storage/' . $item->image_path);
    }
}