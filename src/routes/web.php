<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;


//商品一覧画面の表示 認証ミドルウェア
Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
});
