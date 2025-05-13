<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\Auth\CustomRegisteredUserController;
use App\Http\Controllers\Auth\ResendVerificationEmailController;
use App\Http\Controllers\StripeWebhookController;


//商品一覧画面の表示　認証不要
Route::get('/', [ItemController::class, 'index']);
//商品詳細画面の表示　認証不要
Route::get('/item/{item}', [ItemController::class, 'show']);

//メール認証誘導画面の表示　認証不要
Route::get('/email/verify/notice', function () {
    return view('auth.verify');
});

// カスタム登録ルートで CustomRegisteredUserController を使用するように設定 
Route::middleware(['guest:' . config('fortify.guard')])->group(function () {
    Route::post('/register', [CustomRegisteredUserController::class, 'store']);
});

//Mailhogへのルート
Route::get('/mailhog', function () {
    return redirect('http://localhost:8025');
});

Route::post('/email/verification-notification', [ResendVerificationEmailController::class, 'store'])
    ->middleware(['throttle:6,1'])->name('verification.resend');

//stripeからの通知受け取り
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);


//商品一覧画面の表示 認証ミドルウェア
Route::middleware('auth')->group(function () {
    //プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);
    //マイページ
    Route::get('/mypage', [ProfileController::class, 'index']);
    //出品
    Route::get('/sell', [SellController::class, 'create']);
    Route::post('/sell', [SellController::class, 'store']);
    //いいね機能
    Route::post('/item/{item}/like', [ItemController::class, 'toggleLike']);
    //商品購入画面の表示
    Route::get('/purchase/{item}', [PurchaseController::class, 'show']);

    //商品購入処理
    Route::post('/purchase/{item}', [PurchaseController::class, 'purchase']);
    Route::get('/item/{item}/purchased', [PurchaseController::class, 'purchaseSuccess']);

    //配送先住所変更画面の表示
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'edit']);
    //配送先住所変更処理
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'update']);
    //コメント送信機能
    Route::post('/item/{item}/comment', [ItemController::class, 'store']);
});
