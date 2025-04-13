<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    //商品一覧画面の表示
    public function index()
    {
        return view('item/index');
    }
}
