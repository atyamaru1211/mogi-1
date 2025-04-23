<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Like;

class ItemController extends Controller
{
    //商品一覧画面の表示
    public function index(Request $request)
    {
        $query = Item::query();

        //自身の出品商品を除外
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        //マイリスト表示処理
        if (Auth::check() && $request->query('tab') === 'mylist') {
            $likedItemIds = Auth::user()->likes()->pluck('item_id');
            $query->whereIn('id', $likedItemIds);
        } elseif (!Auth::check() && $request->query('tab') === 'mylist') {
            $items = collect();
            return view('item/index', compact('items'));
        }

        //検索機能
        elseif (!empty($request->keyword)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
            });
        }

        $items = $query->get();

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

        return response()->json(['liked' => !$liked, 'like_count' => $item->likes()->count()]);
    }
}
