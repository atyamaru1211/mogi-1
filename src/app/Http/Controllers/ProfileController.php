<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\View\View;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //マイページ画面表示
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab');
        $soldItems = [];
        $boughtItems = [];

        if ($tab === 'sell') {
            $soldItems = $this->getSellHistory($user);
        } elseif ($tab === 'buy') {
            $boughtItems = $this->getBuyHistory($user);
        } else {
            $soldItems = $this->getSellHistory($user);
        }

        return view('mypage.index', compact('user', 'soldItems', 'boughtItems', 'tab'));
    }


    //プロフィール編集画面表示
    public function edit():View
    {
        $user = auth()->user();
        $profile = $user->profile;
        return view('mypage.edit', compact('profile'));
    }


    //プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $profile = $user->profile()->firstOrNew();
        $dir = 'profiles';

        $oldImagePath = $profile->getOriginal('profile_image_path');
        
        if ($request->hasFile('profile-image')) {
            $file_name = $request->file('profile-image')->getClientOriginalName();
            $path = $request->file('profile-image')->storeAs('public/' . $dir, $file_name);
            $profile->profile_image_path = str_replace('public/', 'storage/', $path); // 保存パスを storage/profiles/~~~ の形式に

            if ($oldImagePath) {
                $oldFilePath = str_replace('storage/', 'public/', $oldImagePath);
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
        }

        $profile->name = $request->input('name');
        $profile->postal_code = $request->input('postal_code');
        $profile->address = $request->input('address');
        $profile->building = $request->input('building');

        $user->profile()->save($profile);

        return redirect('/mypage');
    }
    

    public function getSellHistory($user)
    {
        return Item::where('user_id', $user->id)->latest()->get();
    }

    public function getBuyHistory($user)
    {
        return Purchase::where('buyer_id', $user->id)->with('item')->latest()->get();
    }
         
}
