<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\View\View;
use App\Models\Profile;

class ProfileController extends Controller
{
    //プロフィール画面表示
    public function edit():View
    {
        $user = auth()->user();
        $profile = $user->profile;
        return view('mypage.edit', compact('profile'));
    }

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
}
