<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Like;

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

    //商品詳細画面の表示
    public function show(Item $item)
    {
        return view('item.show', compact('item'));
    }

    //いいね機能
    public function toggleLike(Request $request, Item $item)
    {
        $user = Auth::user();

        $liked = $item->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $item->likes()->where('user_id', $user->id)->delete();
        } else {
            $like = new Like();
            $like->user_id = $user->id;
            $like->item_id = $item->id;
            $like->save();
        }

        return back();
    }
}
