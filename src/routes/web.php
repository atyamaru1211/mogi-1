<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;


//商品一覧画面の表示　認証不要
Route::get('/', [ItemController::class, 'index']);


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

});


//いいね機能
Route::middleware('auth')->post('/item/{item}/like', [ItemController::class, 'toggleLike']);

//商品詳細画面の表示
Route::get('/item/{item}', [ItemController::class, 'show']);

//商品購入画面の表示
Route::middleware('auth')->get('/purchase/{item}', [PurchaseController::class, 'show']);

//商品購入処理
Route::middleware('auth')->post('/purchase/{item}', [PurchaseController::class, 'purchase']);

//配送先住所変更画面の表示
Route::middleware('auth')->get('/purchase/address/{item}', [PurchaseController::class, 'edit']);

//配送先住所変更処理
Route::middleware('auth')->post('/purchase/address/{item}', [PurchaseController::class, 'update']);

//コメント送信機能
Route::middleware('auth')->post('/item/{item}/comment', [ItemController::class, 'store']);