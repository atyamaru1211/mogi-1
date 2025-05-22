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
    public function show(Request $request, Item $item)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $checkoutSession = Session::retrieve($sessionId);

                if ($checkoutSession->payment_status === 'paid') {
                    // ★ 決済が完了しているので、purchases テーブルにデータを保存する処理を実行
                    $user = Auth::user(); // 認証済みユーザーを取得
                    if ($user) {
                        DB::beginTransaction();
                        try {
                            $shippingAddressData = session('shipping_address');
                            $addressId = null;

                            if ($shippingAddressData) {
                                $shippingAddress = Address::updateOrCreate([
                                    'user_id' => $user->id,
                                    'item_id' => $item->id,
                                ], [
                                    'postal_code' => $shippingAddressData['postal_code'],
                                    'address' => $shippingAddressData['address'],
                                    'building' => $shippingAddressData['building']
                                ]);
                                $addressId = $shippingAddress->id;
                            } else {
                                $profile = $user->profile;
                                $shippingAddress = Address::updateOrCreate([
                                    'user_id' => $user->id,
                                    'item_id' => $item->id,
                                ], [
                                    'postal_code' => $profile->postal_code,
                                    'address' => $profile->address,
                                    'building' => $profile->building
                                ]);
                                $addressId = $shippingAddress->id;
                            }

                            $paymentMethodType = session('payment_method_type'); // セッションから取得

                            Purchase::create([
                                'item_id' => $item->id,
                                'seller_id' => $item->user_id,
                                'purchase_price' => $item->price,
                                'address_id' => $addressId,
                                'buyer_id' => $user->id,
                                'payment_method' => $paymentMethodType === 'konbini' ? 'コンビニ払い' : 'カード払い',
                            ]);

                            session()->forget('shipping_address');
                            session()->forget('payment_method_type'); // ★ ここで削除

                            DB::commit();
                            // ★ ここでSold表示などの処理を行う
                        } catch (\Exception $e) {
                            DB::rollback();
                        }
                    }
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                \Log::error('Stripe API エラー (リダイレクト時): ' . $e->getMessage());
            }
        }

        $user = Auth::User();
        $profile = null;
        $shippingAddressFormSession = session('shipping_address');

        if ($user) {
            $profile = $user->profile;
        }

        $data = [
            'item' => $item,
            'user' => $user,
            'profile' => $profile,
            'shippingAddress' => $shippingAddressFormSession,
        ];

        return view('item.show', $data);
    }

    //いいね機能
    public function toggleLike(Request $request, Item $item)
    {
        $user = Auth::user();
        $liked = $item->likes()->where('user_id', $user->id)->exists();

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

        return redirect("/item/{$item->id}");
        //return back();
    }
}
