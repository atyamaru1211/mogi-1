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
use Stripe\Checkout\Session;
use Stripe\Stripe; 


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

    public function updateDisplay(Request $request, Item $item) // ここが Request $request になります
    {
        $request->validate([
            'payment_method' => ['required'],
        ], [
            'payment_method.required' => '支払い方法を選択してください',
        ]);
        return redirect('/purchase/' . $item->id)->withInput();
    }

    public function purchase(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');
        $shippingAddress = session('shipping_address');
        $profile = $user->profile;

        DB::beginTransaction();
        try {
            if ($shippingAddress && !empty($shippingAddress['address'])) {
                $address = Address::updateOrCreate(
                    ['user_id' => $user->id, 'item_id' => $item->id],
                    ['postal_code' => $shippingAddress['postal_code'], 'address' => $shippingAddress['address'], 'building' => $shippingAddress['building']]
                );
                $addressId = $address->id;
            } elseif ($profile && !empty($profile->address)) {
                $address = Address::updateOrCreate(
                    ['user_id' => $user->id, 'item_id' => $item->id],
                    ['postal_code' => $profile->postal_code, 'address' => $profile->address, 'building' => $profile->building]
                );
                $addressId = $address->id;
            } else {
                DB::rollback();
                return redirect('/purchase/' . $item->id);
            }

            $purchase = Purchase::create([
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'seller_id' => $item->user_id,
                'address_id' => $addressId,
                'purchase_price' => $item->price,
                'payment_method' => $paymentMethod,
            ]);

            session()->forget('shipping_address');
            session(['payment_method_type' => $paymentMethod]);

            DB::commit();

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
                'success_url' => url('/item/' . $item->id. '/purchased?purchase_id=' . $purchase->id), 
            ];

            if ($paymentMethod === 'konbini') {
                $checkoutParams['payment_method_types'] = ['konbini'];
            } elseif ($paymentMethod === 'card') {
                $checkoutParams['payment_method_types'] = ['card'];
            }

            $checkoutSession = Session::create($checkoutParams);
            return redirect()->away($checkoutSession->url);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollback(); 
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/purchase/' . $item->id);
        }
    }

    public function purchaseSuccess(Request $request, Item $item)
    {
        $purchaseId = $request->query('purchase_id');
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $purchase = Purchase::find($purchaseId);

            if (!$purchase || $purchase->buyer_id !== $user->id || $purchase->item_id !== $item->id) {
                DB::rollback();
                return redirect('/purchase/' . $item->id);
            }

            session()->forget('shipping_address');
            session()->forget('payment_method_type');

            DB::commit();

            return redirect('/item/' . $item->id);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/purchase/' . $item->id);
        }
    }
}
