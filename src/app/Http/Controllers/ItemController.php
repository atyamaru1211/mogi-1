<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    //商品一覧画面の表示
    public function index()
    {
        $items = Item::query();

        if (Auth::check()) {
            $items->where('user_id', '!=', Auth::id());
        }

        $items = $items->get();
        
        return view('item/index', compact('items'));
    }
}
