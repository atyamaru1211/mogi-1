<?php

namespace Tests\Feature\Product;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Database\Seeders\ItemsSeeder;
use Database\Factories\AddressFactory;

class ProductListTest extends TestCase
{
    use DatabaseTransactions;

    // ID:4 商品一覧取得
    // 全商品を取得できる
    public function testCanRetrieveAllProducts()
    {
        $this->seed(ItemsSeeder::class);

        $response = $this->get('/');

        $itemCount = Item::count();
        $response->assertViewHas('items');
        $this->assertCount($itemCount, $response['items']);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
            $response->assertSee($item->name, 'alt');
            $response->assertSee('storage/' . $item->image_path);
        }
    }

    // 購入済み商品は「Sold」と表示される
    public function testSoldItemsAreDisplayedWithSoldLabel()
    {
        $this->seed(ItemsSeeder::class);

        $buyer = User::factory()->create();

        $soldItem = Item::first();
        Purchase::factory()->create([
            'item_id' => $soldItem->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $soldItem->user_id,
            'address_id' => AddressFactory::new()->create()->id,
        ]);

        $response = $this->get('/');

        $response->assertSee($soldItem->name);
        $response->assertSee('Sold');
    }

    // 自分が出品した商品は表示されない
    public function testOwnItemsAreNotDisplayedInList()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ownItem = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト_自分の出品商品',
            'description' => 'これは自分が出品したテスト商品です。',
            'price' => 5000,
            'condition' => 1,
            'image_path' => 'my_test_item.jpg',
        ]);

        $response = $this->get('/');

        $response->assertDontSee($ownItem->name);
        $response->assertDontSee('src="' . asset('storage/' . $ownItem->image_path) . '"');
        $response->assertDontSee('alt="' . e($ownItem->name) . '"');
    }
}