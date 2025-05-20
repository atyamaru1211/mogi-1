<?php

namespace Tests\Feature\Product;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategoriesSeeder;
use Database\Seeders\ItemsSeeder;
use Database\Seeders\ItemCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CategoriesSeeder::class);
        $this->seed(ItemsSeeder::class);
        $this->seed(ItemCategorySeeder::class);
    }

    // ID:7
    // 必要な情報が表示される
    public function testRequiredInformationIsDisplayedOnItemDetailPage()
    {
        $this->seed(ItemsSeeder::class, CategoriesSeeder::class);

        $item = Item::first();
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)->state(['item_id' => $item->id, 'user_id' => $user->id])->create();

        $categories = $item->categories;

        $response = $this->get('/item/' . $item->id);

        $response->assertSee('/storage/items/watch.jpeg');

        $response->assertSee($item->name);

        $response->assertSee($item->brand);

        $response->assertSee(number_format($item->price));

        $response->assertSee($item->likes()->count());

        $response->assertSee($item->comments()->count());

        $response->assertSee($item->description);

        foreach ($item->categories as $category) {
            $response->assertSee($category->name);
        }

        $conditionText = '';
        switch ($item->condition) {
            case 1:
                $conditionText = '良好';
                break;
            case 2:
                $conditionText = '目立った傷や汚れなし';
                break;
            case 3:
                $conditionText = 'やや傷や汚れあり';
                break;
            case 4:
                $conditionText = '状態が悪い';
                break;
        }
        $response->assertSee($conditionText);

        foreach ($comments as $comment) {
            $response->assertSee($comment->user->name);
            $response->assertSee($comment->content);
        }
    }

    // 複数選択されたカテゴリが表示されているか
    public function testMultipleCategoriesAreDisplayedOnItemDetailPage()
    {
        $item = Item::findOrFail(1);
        
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        foreach ($item->categories as $category) {
            $response->assertSee($category->name);
        }

        $response->assertSeeInOrder(
            $item->categories->pluck('name')->toArray()
        );
    }
}