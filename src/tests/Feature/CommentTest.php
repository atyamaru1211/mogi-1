<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
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
        $response->assertSee($commentContent);
    }

    // ログイン前のユーザーはコメントを送信できない
    public function testGuestUserCannotSubmitComment()
    {
        $this->seed(ItemsSeeder::class);
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::create([
            'user_id' => $user->id, 
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 1000,
            'condition' => 1,
            'image_path' => 'dummy_image.jpg',
        ]);
        
        $this->get("/item/{$item->id}");

        $response = $this->post("/item/{$item->id}/comment", [ 
            'content' => '', 
        ]);

        $response->assertRedirect("/item/" . $item->id);

        $response->assertSessionHasErrors('content');

        $followedResponse = $this->followRedirects($response);
        $followedResponse->assertSeeText('商品コメントを入力してください'); 
    }

    // コメントが255文字の場合、バリデーションメッセージが表示される
    public function testCommentContentHasMaxLengthValidation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 1000,
            'condition' => 1,
            'image_path' => 'dummy_image.jpg',
        ]);

        $longCommentContent = str_repeat('あ', 256);

        $this->get("/item/{$item->id}");

        $response = $this->post("/item/{$item->id}/comment", [
            'content' => $longCommentContent,
        ]);

        $response->assertSessionHasErrors('content');

        $response->assertRedirect("/item/{$item->id}");

        $followedResponse = $this->followRedirects($response);

        $followedResponse->assertSeeText('商品コメントは255文字以内で入力してください');
    }
}