<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Address;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::User();
        $profile = null;

        if ($user) {
            $profile = $user->profile;
        }

        $data = [
            'item' => $item,
            'user' => $user,
            'profile' => $profile,
            'shippingAddress' => null,
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

        $user->address()->updateOrCreate([], [
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        return redirect('/purchase/' . $item->id);
    }
}
