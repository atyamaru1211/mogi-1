<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Database\Factories\AddressFactory;

class MylistTest extends TestCase
{
    use DatabaseTransactions;

    // ID:5 マイリスト一覧取得
    // いいねした商品だけが表示される
    public function testOnlyLikedItemsAreDisplayedOnMylist()
    {
        $this->seed(ItemsSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $likedItem1 = Item::first();
        $likedItem2 = Item::skip(1)->first();
        $unlikedItem = Item::skip(2)->first();

        $user->likes()->attach([$likedItem1->id, $likedItem2->id]);

        $response = $this->get('/?tab=mylist'); 

        $response->assertSee($likedItem1->name);
        $response->assertSee($likedItem2->name);
        $response->assertDontSee($unlikedItem->name);
    }

    // 購入済み商品は「Sold」と表示される
    public function testSoldLabelIsDisplayedForPurchasedItemsOnMylist()
    {
        $this->seed(ItemsSeeder::class);

        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $purchasedItem = Item::first();

        Purchase::factory()->create([
            'item_id' => $purchasedItem->id,
            'buyer_id' => $buyer->id,
            'seller_id' => User::factory()->create()->id,
            'address_id' => AddressFactory::new()->create()->id,
        ]);

        $buyer->likes()->attach($purchasedItem->id);
        $response = $this->get('/?tab=mylist');
        $response->assertSee($purchasedItem->name);
        $response->assertSee('Sold');
    }

    // 自分が出品した商品は表示されない
    public function testOwnItemsAreNotDisplayedOnMylist()
    {
        $seller = User::factory()->create();
        $this->actingAs($seller);

        $ownItem = Item::create([
            'user_id' => $seller->id,
            'name' => '私の出品商品',
            'description' => 'これは私が出品した商品です。',
            'price' => 2000,
            'condition' => 1,
            'image_path' => 'my_item.jpg',
        ]);

        $seller->likes()->attach($ownItem->id);

        $response = $this->get('/?tab=mylist');

        $response->assertDontSee($ownItem->name);
        $response->assertDontSee('<img src="' . asset('storage/' . $ownItem->image_path) . '"');

    }

    // 未認証の場合は何も表示されない
    public function testGuestUserSeesNothingOnMylist()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('.product-item');
    }
}