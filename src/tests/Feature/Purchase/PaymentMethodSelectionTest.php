<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use App\Models\Profile;
use Database\Seeders\ItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    // ID:11 支払い方法選択機能
    // 小計画面で変更が即時反映される
    public function testPaymentMethodIsReflectedOnSubtotalScreen()
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $buyer->profile()->create([
            'name' => 'テストユーザー名',
            'postal_code' => '123-4567',
            'address' => '東京都墨田区押上',
            'building' => 'スカイツリービル1F',
        ]);

        $seller = User::factory()->create();

        $this->seed(ItemsSeeder::class);
        $item = Item::first();
        $item->update(['user_id' => $seller->id]);

        $address = Address::factory()->create([
            'user_id' => $buyer->id,
            'postal_code' => '123-4567',
            'address' => '東京都墨田区押上',
            'building' => 'スカイツリービル1F',
        ]);

        // 1. 支払い方法選択画面を開く（GETリクエスト）
        $response = $this->get("/purchase/{$item->id}"); 
        $response->assertSee("選択してください");

        // 2. プルダウンメニューから支払い方法を選択する（POSTリクエスト）
        $responsePostKonbini = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini', // コンビニ払いを選択
            'address_id' => $address->id, 
            'purchase_price' => $item->price, 
            'address_existence' => 'exists',
        ]);

        // 同じ購入画面にリダイレクトされることを確認
        $responsePostKonbini->assertRedirect("/purchase/{$item->id}");

        // リダイレクト後のレスポンス（つまり、再度GETで取得されるHTML）
        // Laravelのテストヘルパーは、POST後にリダイレクトされた先のページを自動的に追跡します。
        // なので、$responsePostKonbini の内容がリダイレクト先のHTMLです。
        $followedResponse = $this->followRedirects($responsePostKonbini);

        $followedResponse->assertSee('value="konbini" selected');
        // または、よりシンプルに selected 属性の有無を確認する
        // $followedResponse->assertSee('value="konbini" selected');

        // ※ JavaScriptで動的に表示が変わる部分のテストは、
        //    ブラウザテスト（Laravel Duskなど）で行うのが一般的です。
        //    この機能テストでは、サーバーサイドのレンダリングが正しいかを検証します。
    }

    /*public function testPaymentMethodIsReflectedOnSubtotalScreen()
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $buyer->profile()->create([
            'name' => 'テストユーザー名',
            'postal_code' => '123-4567',
            'address' => '東京都墨田区押上',
            'building' => 'スカイツリービル1F',
        ]);

        $seller = User::factory()->create();

        $this->seed(ItemsSeeder::class);
        $item = Item::first();
        $item->update(['user_id' => $seller->id]);

        $address = Address::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都墨田区押上',
            'building' => 'スカイツリービル1F',
        ]);

        $response = $this->get("/purchase/{$item->id}"); 

        $response->assertSee("選択してください");

        $responsePostKonbini = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini',
            'address_id' => $address->id, 
            'purchase_price' => $item->price, 
            'address_existence' => 'exists',
        ]);

        $responsePostKonbini->assertRedirectContains('https://checkout.stripe.com');

        $this->withSession(['payment_method_type' => 'konbini'])
             ->actingAs($buyer)
             ->get("/item/{$item->id}/purchased");

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'コンビニ払い',
        ]);

    }*/

}