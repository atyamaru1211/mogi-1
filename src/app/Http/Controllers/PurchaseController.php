<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Address;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session; //★
use Stripe\Stripe; //★


class PurchaseController extends Controller
{
    public function show(Request $request, Item $item)
    {
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

        return view('item.purchase', $data);
    }

    public function edit(Item $item)
    {
        $user = Auth::user();
        $shippingAddress = $user->address;

        return view('purchase.address', [
            'item' => $item,
            'shippingAddress' => $shippingAddress,
        ]);
    }

    public function update(AddressRequest $request, Item $item)
    {
        $user = Auth::user();

        $addressData = [
            'item_id' => $item->id,
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ];

        $user->address()->updateOrCreate(['item_id' => $item->id], $addressData);

        session(['shipping_address' => $addressData]);

        return redirect('/purchase/' . $item->id);
    }


    public function purchase(PurchaseRequest $request, Item $item)
    {
        $paymentMethod = $request->input('payment_method');

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ],
        ];

        $checkoutParams = [
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => url('/item/' . $item->id . '/purchased'),
        ];

        if ($paymentMethod === 'konbini') {
            $checkoutParams['payment_method_types'] = ['konbini'];
        } elseif ($paymentMethod === 'card') {
            $checkoutParams['payment_method_types'] = ['card'];
        }

        try {
            $checkoutSession = Session::create($checkoutParams);
            return redirect()->away($checkoutSession->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return back();
        }
    }

    public function purchaseSuccess(Item $item)
        {
            $user = Auth::user();
            $shippingAddressData = session('shipping_address');
            $addressId = null;

            DB::beginTransaction();
            try {
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
                Purchase::create([
                    'item_id' => $item->id,
                    'seller_id' => $item->user_id,
                    'purchase_price' => $item->price,
                    'address_id' => $addressId,
                    'buyer_id' => $user->id,
                    'payment_method' => request()->input('payment_method_type') === 'konbini' ? 'コンビニ払い' : 'カード払い', // 支払い方法を保存
                ]);

                session()->forget('shipping_address');

                $item->buyer_id = $user->id;
                $item->save();

                DB::commit();

                return redirect('/item/' . $item->id)->with('success_sold', true);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect('/purchase/' . $item->id)->with('error', '購入処理中にエラーが発生しました。');
            }
        }

        /*
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');
        $shippingAddressData = session('shipping_address');
        $addressId = null;

        //既に購入済か確認
        $alreadyPurchased = Purchase::where('buyer_id', $user->id)->where('item_id', $item->id)->exists();

        if ($alreadyPurchased || $item->buyer_id !== null) {
            //既に購入済の場合、何もしないでリダイレクト
            return response()->json(['status' => 'success']);
        }

        DB::beginTransaction();

        try {
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

            $purchaseData = $request->only(['payment_method']);
            $purchaseData['item_id'] = $item->id;
            $purchaseData['seller_id'] = $item->user_id;
            $purchaseData['purchase_price'] = $item->price;
            $purchaseData['address_id'] = $addressId;
            $purchaseData['buyer_id'] = $user->id; 
            
            Purchase::create($purchaseData);

            session()->forget('shipping_address');

            //商品のbuyer_idを更新し売れきれ状態にする
            $item->buyer_id = $user->id;
            $item->save();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => '購入処理中にエラーが発生しました']);
        }
    }*/
}
