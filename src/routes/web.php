<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;


//商品一覧画面の表示　認証不要
Route::get('/', [ItemController::class, 'index']);

//商品一覧画面の表示 認証ミドルウェア
Route::middleware('auth')->group(function () {
   // Route::get('/', [ItemController::class, 'index']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);
});
