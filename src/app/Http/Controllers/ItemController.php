<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;

class ItemController extends Controller
{
    //商品一覧画面の表示
    public function index(Request $request)
    {
        $query = Item::query();
        $keyword = $request->keyword;
        $tab = $request->query('tab');

        //自身の出品商品を除外
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        //マイリスト表示処理
        if (Auth::check() && $request->query('tab') === 'mylist') {
            $likedItemIds = Auth::user()->likes()->pluck('item_id');
            $query->whereIn('id', $likedItemIds);

            if (session()->has('search_keyword')) {
                $keyword = session('search_keyword');
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            }
        } elseif (!Auth::check() && $request->query('tab') === 'mylist') {
            $items = collect();
            return view('item/index', compact('items'));
        }

        //検索機能
        elseif (!empty($request->keyword)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%');
            });
            session(['search_keyword' => $keyword]);
        } else {
            session()->forget('search_keyword');
        }

        $items = $query->get();

        return view('item/index', compact('items', 'keyword', 'tab'));
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

    /*    if ($liked) {
            $item->likes()->where('user_id', $user->id)->delete();
        } else {
            $like = new Like();
            $like->user_id = $user->id;
            $like->item_id = $item->id;
            $like->save();
        }*/

        if ($liked) {
            $item->likes()->detach($user->id);
        } else {
            $item->likes()->attach($user->id);
        }

        return response()->json(['liked' => !$liked, 'like_count' => $item->likes()->count()]);
    }

    //コメント機能
    public function store(CommentRequest $request, Item $item)
    {
        $comment = new Comment();
        $comment->item_id = $item->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->input('content');
        $comment->save();

        return back();
    }
}
