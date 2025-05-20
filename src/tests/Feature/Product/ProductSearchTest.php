<?php

namespace Tests\Feature\Product;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use DatabaseTransactions;

    // 商品検索機能
    // 「商品名」で部分一致検索ができる
    public function testCanSearchProductsByNameWithPartialMatch()
    {
        $this->seed(ItemsSeeder::class);

        $blueBook = Item::where('name', 'like', '%青い本%')->first();
        $redBook = Item::where('name', 'like', '%赤い本%')->first();
        $bluePen = Item::where('name', 'like', '%青いペン%')->first();

        $response = $this->get('/?keyword=青い');

        $response->assertOk();
        if ($blueBook) {
            $response->assertSee($blueBook->name);
            $response->assertSee('<img src="' . asset('storage/' . $blueBook->image_path) . '"');
        }
        if ($bluePen) {
            $response->assertSee($bluePen->name);
            $response->assertSee('<img src="' . asset('storage/' . $bluePen->image_path) . '"');
        }
        if ($redBook) {
            $response->assertDontSee($redBook->name);
            $response->assertDontSee('<img src="' . asset('storage/' . $redBook->image_path) . '"');
        }

        $response = $this->get('/?keyword=赤い');

        $response->assertOk();
        if ($redBook) {
            $response->assertSee($redBook->name);
            $response->assertSee('<img src="' . asset('storage/' . $redBook->image_path) . '"');
        }
        if ($blueBook) {
            $response->assertDontSee($blueBook->name);
            $response->assertDontSee('<img src="' . asset('storage/' . $blueBook->image_path) . '"');
        }
        if ($bluePen) {
            $response->assertDontSee($bluePen->name);
            $response->assertDontSee('<img src="' . asset('storage/' . $bluePen->image_path) . '"');
        }

        $response = $this->get('/?keyword=本');

        $response->assertOk();
        if ($blueBook) {
            $response->assertSee($blueBook->name);
            $response->assertSee('<img src="' . asset('storage/' . $blueBook->image_path) . '"');
        }
        if ($redBook) {
            $response->assertSee($redBook->name);
            $response->assertSee('<img src="' . asset('storage/' . $redBook->image_path) . '"');
        }
        if ($bluePen) {
            $response->assertDontSee($bluePen->name);
            $response->assertDontSee('<img src="' . asset('storage/' . $bluePen->image_path) . '"');
        }
    }

    // 検索状態がマイリストでも保持されている
    public function testSearchKeywordIsRetainedOnMylist()
    {
        $this->seed(ItemsSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $blueBook = Item::where('name', 'like', '%青い本%')->first();
        $redBook = Item::where('name', 'like', '%赤い本%')->first();
        $bluePen = Item::where('name', 'like', '%青いペン%')->first();

        $searchKeyword = '青い';
        $response = $this->get('/?keyword=' . $searchKeyword);
        $response->assertSessionHas('search_keyword', $searchKeyword);

        $response = $this->get('/?tab=mylist&keyword=' . $searchKeyword);
        $this->assertEquals($searchKeyword, session('search_keyword'));

        $likedItemIds = $user->likes()->pluck('item_id');

        if ($blueBook && $likedItemIds->contains($blueBook->id)) {
            $response->assertSee($blueBook->name);
            $response->assertSee('<img src="' . asset('storage/' . $blueBook->image_path) . '"');
        }
        if ($bluePen && $likedItemIds->contains($bluePen->id)) {
            $response->assertSee($bluePen->name);
            $response->assertSee('<img src="' . asset('storage/' . $bluePen->image_path) . '"');
        }
        if ($redBook && $likedItemIds->contains($redBook->id)) {
            $response->assertDontSee($redBook->name);
            $response->assertDontSee('<img src="' . asset('storage/' . $redBook->image_path) . '"');
        }
    }

}