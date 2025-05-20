<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // ID:9 コメント送信機能
    // ログイン済のユーザーはコメントを送信できる
    public function testLoggedInUserCanPostCommentAndCommentCountIncreases()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->seed(ItemsSeeder::class);
        $item = Item::first();

        $commentContent = 'これはテストコメントです。';

        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => $commentContent,
        ]);

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => $commentContent,
        ]);

        $response = $this->get('/item/' . $item->id);
        $response->assertSee('(1)');

        $response = $this->get('/item/' . $item->id);
        $response->assertSee($commentContent);
    }
}