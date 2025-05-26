<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use App\Models\Profile;
use App\Models\Purchase;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseItemTest extends TestCase
{
    use RefreshDatabase;

    // ID:10 商品購入機能
    // ★「購入する」ボタンを押下するとstripeの購入処理画面に遷移し、購入処理後、購入が完了する★　README参照
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
            'postal_code' => $buyer->profile->postal_code,
            'address' => $buyer->profile->address,
            'building' => $buyer->profile->building,
        ]);

        $this->get("/purchase/{$item->id}");

        $paymentMethod = 'card';
        $response = $this->post("/purchase/{$item->id}/checkout", [
            'payment_method' => $paymentMethod,
            'purchase_price' => $item->price,
            'address_existence' => 'exists',
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com');
        
        $responseAfterStripe = $this->actingAs($buyer)->get("/item/{$item->id}/purchased");
        $responseAfterStripe->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'purchase_price' => $item->price,
            'payment_method' => $paymentMethod,
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
            'postal_code' => $buyer->profile->postal_code,
            'address' => $buyer->profile->address,
            'building' => $buyer->profile->building,
        ]);

        $this->get("/purchase/{$item->id}");

        $paymentMethod = 'card';

        $response = $this->post("/purchase/{$item->id}/checkout", [
            'payment_method' => $paymentMethod,
            'purchase_price' => $item->price,
            'address_existence' => 'exists',
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com');

        $responseAfterStripe = $this->actingAs($buyer)->get("/item/{$item->id}/purchased");
        $responseAfterStripe->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'purchase_price' => $item->price,
            'payment_method' => $paymentMethod,
        ]);

        $item = $item->fresh();

        $indexResponse = $this->get('/');

        $indexResponse->assertSee('storage/' . $item->image_path);

        $indexResponse->assertSee('Sold');
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
            'postal_code' => $buyer->profile->postal_code,
            'address' => $buyer->profile->address,
            'building' => $buyer->profile->building,
        ]);

        $this->get("/purchase/{$item->id}");

        $purchasePrice = $item->price;
        $paymentMethod = 'card';

        $response = $this->post("/purchase/{$item->id}/checkout", [
            'payment_method' => $paymentMethod,
            'purchase_price' => $purchasePrice,
            'address_existence' => 'exists',
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com');

        $responseAfterStripe = $this->actingAs($buyer)->get("/item/{$item->id}/purchased");
        $responseAfterStripe->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'purchase_price' => $purchasePrice,
            'payment_method' => $paymentMethod,
        ]);

        $mypageResponse = $this->actingAs($buyer)->get('/mypage?tab=buy');
        $mypageResponse->assertSeeText($item->name);
        $mypageResponse->assertSee('storage/' . $item->image_path);
    }
}