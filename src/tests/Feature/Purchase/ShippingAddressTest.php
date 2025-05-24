<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use App\Models\Profile;
use App\Models\Purchase;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    // ID: 送付先住所変更機能
    // 送付先住所変更画面にて登録した住所が商品購入画面に反映される
    public function testRegisteredAddressIsReflectedOnPurchaseScreen()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->profile()->create([
            'name' => '初期ユーザー名',
            'postal_code' => '000-0000',
            'address' => '初期住所',
            'building' => '初期建物名',
        ]);

        $this->seed(ItemsSeeder::class);
        $item = Item::first();
        $item->update(['user_id' => User::factory()->create()->id]);

        $responseEdit = $this->get("/purchase/address/{$item->id}");

        $newPostalCode = '123-4567';
        $newAddress = '東京都墨田区押上1-1-1';
        $newBuilding = 'スカイツリータワー10F';

        $responseUpdate = $this->post("/purchase/address/{$item->id}", [
            'postal_code' => $newPostalCode,
            'address' => $newAddress,
            'building' => $newBuilding,
        ]);

        $responseUpdate->assertRedirect("/purchase/{$item->id}");

        $responseUpdate->assertSessionHas('shipping_address', [
            'item_id' => $item->id,
            'postal_code' => $newPostalCode,
            'address' => $newAddress,
            'building' => $newBuilding,
        ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => $newPostalCode,
            'address' => $newAddress,
            'building' => $newBuilding,
        ]);

        $responsePurchase = $this->get("/purchase/{$item->id}");

        $responsePurchase->assertSeeText($newPostalCode);
        $responsePurchase->assertSeeText($newAddress);
        $responsePurchase->assertSeeText($newBuilding);
    }

    // 購入した商品に送付先住所が紐づいて登録される
    public function testPurchasedItemIsAssociatedWithCorrectShippingAddress()
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $buyer->profile()->create([
            'name' => 'テスト購入者',
            'postal_code' => '000-0000',
            'address' => '東京都',
            'building' => '初期建物',
        ]);

        $this->seed(ItemsSeeder::class);
        $item = Item::first();
        $seller = User::factory()->create();
        $item->update(['user_id' => $seller->id]);

        $newPostalCode = '987-6543';
        $newAddress = '大阪府大阪市中央区';
        $newBuilding = '大阪城';

        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => $newPostalCode,
            'address' => $newAddress,
            'building' => $newBuilding,
        ])->assertRedirect("/purchase/{$item->id}");

        $registeredAddress = Address::where('user_id', $buyer->id)
                                    ->where('item_id', $item->id)
                                    ->first();
        
        $expectedAddressId = $registeredAddress->id;

        $paymentMethod = 'card';
        Session::put('payment_method_type', $paymentMethod);

        $responseCheckout = $this->post("/purchase/{$item->id}/checkout", [
            'payment_method' => $paymentMethod,
            'purchase_price' => $item->price,
            'address_existence' => 'exists',
        ]);

        $responseCheckout->assertRedirectContains('https://checkout.stripe.com');

        $purchase = Purchase::where('item_id', $item->id)
                            ->where('buyer_id', $buyer->id)
                            ->first();
        $responseAfterStripe = $this->actingAs($buyer)->get("/item/{$item->id}/purchased?purchase_id={$purchase->id}");

        $responseAfterStripe->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'purchase_price' => $item->price,
            'address_id' => $expectedAddressId,
            'payment_method' => $paymentMethod,
        ]);
    }
}