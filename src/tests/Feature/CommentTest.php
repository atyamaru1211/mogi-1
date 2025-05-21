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

    // ログイン前のユーザーはコメントを送信できない
    public function testGuestUserCannotSubmitComment()
    {
        $item = Item::first();

        $commentContent = 'テストコメント';

        $response = $this->post("/item/{item}/comment", [
            'content' => $commentContent,
        ]);

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item,
            'content' => $commentContent,
        ]);
    }

    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function testCommentContentIsRequired()
    {
        $item = Item::first();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post("/item/{item}/comment", [
            'content' => '',
        ]);

        $response->assertRedirect('/item/' . $item->id); // 具体的な ID を使用
        $response->assertSessionHasErrors('content');
        $response->assertSee('商品コメントを入力してください');
        //$this->assertStringContainsString('商品コメントを入力してください', session('errors')->first('content'));
        //$this->assertEquals(session('errors')->first('content'), '商品コメントを入力してください');
        //$response->assertSee('商品コメントを入力してください');
        //$response->assertInvalid(['content', '商品コメントを入力してください']);
    }
}