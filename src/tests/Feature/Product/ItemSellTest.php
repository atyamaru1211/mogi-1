<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Database\Seeders\CategoriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemSellTest extends TestCase
{
    use RefreshDatabase;

    // ID:15 出品商品情報登録
    // 商品出品画面にて必要な情報が保存できること
    public function testRequiredItemInformationCanBeStored()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->seed(CategoriesSeeder::class);

        $categories = Category::all();
        $category1 = $categories->get(0);
        $category2 = $categories->get(1);

        Storage::fake('public');

        $imageFile = UploadedFile::fake()->create(
            'test_item_image.jpg',
            0,                     
            'image/jpeg'           
        );

        Storage::disk('public')->putFileAs('items', $imageFile, $imageFile->hashName());

        $itemData = [
            'name' => 'テスト商品名',
            'brand' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。非常に良い状態です。',
            'price' => 1500,
            'image_upload' => $imageFile,
            'condition' => '1',
            'category' => [$category1->id, $category2->id],
        ];

        $response = $this->post('/sell', $itemData);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品名',
            'brand' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。非常に良い状態です。',
            'price' => 1500,
            'condition' => '1',
            'image_path' => 'items/' . $imageFile->hashName(),
        ]);

        $item = Item::where('name', 'テスト商品名')->first();
        $this->assertNotNull($item);

        Storage::disk('public')->assertExists('items/' . $imageFile->hashName());

        $this->assertCount(2, $item->categories);
        $this->assertTrue($item->categories->contains($category1));
        $this->assertTrue($item->categories->contains($category2));
    }
}