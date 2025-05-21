<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    // ID:8 いいね機能
    // いいねアイコンを押下することによって、いいねした商品として登録することができる
    public function testUserCanLikeAnItem()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->seed(ItemsSeeder::class);
        $item = Item::first();

        $response = $this->post('/item/' . $item->id . '/like');

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $this->assertEquals(1, $item->likes()->count());
    }

    // 追加済みのアイコンは色が変化する
    public function testLikedIconShowsDifferentState()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->seed(ItemsSeeder::class);
        $item = Item::first(); 
        $user->likes()->attach($item);

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        $response->assertSee('<button class="like-button liked" id="like-button-' . $item->id . '">', false);

        $response->assertSee('<object class="like-button__icon" id="like-icon-' . $item->id . '" type="image/svg+xml" data="' . asset('images/star_after.svg') . '" alt="いいね"></object>', false);
    }

    // 再度いいねアイコンを押下することによって、いいねを解除することができる
    public function testUserCanUnlikeAnItem()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->seed(ItemsSeeder::class);
        $item = Item::first();
        $user->likes()->attach($item);

        $response = $this->post('/item/' . $item->id . '/like');

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/item/' . $item->id);
        $response->assertSee('<button class="like-button " id="like-button-' . $item->id . '">', false);
        $response->assertSee('<object class="like-button__icon" id="like-icon-' . $item->id . '" type="image/svg+xml" data="' . asset('images/star.svg') . '" alt="いいね"></object>', false);

        $this->assertEquals(0, $item->likes()->count());
    }
}