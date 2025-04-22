<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\View\View;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //マイページ画面表示
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab');

        if ($tab === 'sell') {
            return $this->sellHistory();
        } elseif ($tab === 'buy') {
            return $this->buyHistory();
        } else {
            return view('mypage.index', ['user' => $user]);
        }
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
        
        if ($request->hasFile('profile-image')) {
            $file_name = $request->file('profile-image')->getClientOriginalName();
            $request->file('profile-image')->storeAs('public/' . $dir, $file_name);
            $profile->profile_image_path = 'storage/' . $dir . '/' . $file_name;

            if ($profile->getOriginal('profile_image_path')) {
                Storage::disk('public')->delete($profile->getOriginal('profile_image_path'));
            }
        }

        $profile->name = $request->input('username');
        $profile->postal_code = $request->input('postal_code');
        $profile->address = $request->input('address');
        $profile->building = $request->input('building');

        $user->profile()->save($profile);

        return redirect('/');
    }

    /*
        public function buyHistory()
    {
        $user = Auth::user();
        $purchases = $user->purchases()->latest()->get(); // 例：購入履歴を取得
        return view('mypage.buyHistory', compact('purchases'));
    }

    public function sellHistory()
    {
        $user = Auth::user();
        $items = $user->items()->latest()->get(); // 例：出品履歴を取得
        return view('mypage.sellHistory', compact('items'));
    }
         */
}
