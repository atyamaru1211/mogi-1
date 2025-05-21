<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Address;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileDisplayTest extends TestCase
{
    use RefreshDatabase;

    // ID:13 ユーザー情報取得
    // 必要な情報が取得できる
    public function testUserProfileInformationAndItemsAreDisplayedCorrectly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');
        $profileImage = UploadedFile::fake()->create(
            'profile_picture.jpg',
            0,
            'image/jpeg'
        );

        $storedFileName = $profileImage->hashName();
        $profileImagePath = 'storage/profiles/' . $storedFileName; 

        Storage::disk('public')->putFileAs('profiles', $profileImage, $storedFileName);

        $profileName = 'テストプロフィール名';
        $user->profile()->create([
            'profile_image_path' => $profileImagePath,
            'name' => $profileName,
            'postal_code' => '123-4567',
            'address' => '東京都港区',
            'building' => 'テストビル',
        ]);

        $this->seed(ItemsSeeder::class);

        $sellItem1 = Item::latest()->first();
        $sellItem1->update(['user_id' => $user->id]);
        $sellItem2 = Item::latest()->skip(1)->first();
        $sellItem2->update(['user_id' => $user->id]);

        $otherSeller1 = User::factory()->create();
        $otherSeller2 = User::factory()->create();

        $buyItem1 = Item::latest()->skip(2)->first();
        $buyItem1->update(['user_id' => $otherSeller1->id]);
        $buyItem2 = Item::latest()->skip(3)->first();
        $buyItem2->update(['user_id' => $otherSeller2->id]);

        $addressForBuyItem1 = Address::factory()->create([
            'user_id' => $user->id,
            'item_id' => $buyItem1->id,
        ]);

        $addressForBuyItem2 = Address::factory()->create([
            'user_id' => $user->id,
            'item_id' => $buyItem2->id,
        ]);

        Purchase::factory()->create([
            'item_id' => $buyItem1->id,
            'buyer_id' => $user->id,
            'seller_id' => $otherSeller1->id,
            'purchase_price' => $buyItem1->price,
            'address_id' => 1,
            'payment_method' => 'カード払い',
        ]);
        Purchase::factory()->create([
            'item_id' => $buyItem2->id,
            'buyer_id' => $user->id,
            'seller_id' => $otherSeller2->id,
            'purchase_price' => $buyItem2->price,
            'address_id' => 1,
            'payment_method' => 'コンビニ払い',
        ]);

        $responseSellTab = $this->get('/mypage?tab=sell');

        $responseSellTab->assertSee('background-image: url(\'' . asset($profileImagePath) . '\')', false);
        $responseSellTab->assertSeeText($profileName);

        $responseSellTab->assertSeeText($sellItem1->name);
        $responseSellTab->assertSee('storage/' . $sellItem1->image_path);
        $responseSellTab->assertSeeText($sellItem2->name);
        $responseSellTab->assertSee('storage/' . $sellItem2->image_path);

        $responseBuyTab = $this->get('/mypage?tab=buy');

        $responseBuyTab->assertSee('background-image: url(\'' . asset($profileImagePath) . '\')', false);
        $responseBuyTab->assertSeeText($profileName);

        $responseBuyTab->assertSeeText($buyItem1->name);
        $responseBuyTab->assertSee('storage/' . $buyItem1->image_path);
        $responseBuyTab->assertSeeText($buyItem2->name);
        $responseBuyTab->assertSee('storage/' . $buyItem2->image_path);
    }
}