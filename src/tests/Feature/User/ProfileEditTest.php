<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;
    
    // ID:14 ユーザー情報変更
    // 変更項目が初期値として過去設定されていること
    public function testProfileEditFormDisplaysCorrectInitialValues()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public'); 
        $profileImage = UploadedFile::fake()->create(
            'initial_profile_pic.jpg',
            0,
            'image/jpeg'
        );

        $storedFileName = $profileImage->hashName();
        $profileImagePath = 'storage/profiles/' . $storedFileName;

        Storage::disk('public')->putFileAs('profiles', $profileImage, $storedFileName);

        $initialProfileName = '初期ユーザー名';
        $initialPostalCode = '123-0001';
        $initialAddress = '東京都テスト区テスト町1-2-3';
        $initialBuilding = 'テストビルディング101';

        $user->profile()->create([
            'profile_image_path' => $profileImagePath,
            'name' => $initialProfileName,
            'postal_code' => $initialPostalCode,
            'address' => $initialAddress,
            'building' => $initialBuilding,
        ]);

        $response = $this->get('/mypage/profile');

        $response->assertSee('background-image: url(\'' . asset($profileImagePath) . '\')', false);

        $response->assertSeeInOrder([
            '<label class="form-label" for="name">ユーザー名</label>',
            '<input class="form-input" type="text" name="name" id="name" value="' . $initialProfileName . '">'
        ], false);

        $response->assertSeeInOrder([
            '<label class="form-label" for="postal_code">郵便番号</label>',
            '<input class="form-input" type="text" name="postal_code" id="postal_code" value="' . $initialPostalCode . '">'
        ], false);

        $response->assertSeeInOrder([
            '<label class="form-label" for="address">住所</label>',
            '<input class="form-input" type="text" name="address" id="address" value="' . $initialAddress . '">'
        ], false);

        $response->assertSeeInOrder([
            '<label class="form-label" for="building">建物名</label>',
            '<input class="form-input" type="text" name="building" id="building" value="' . $initialBuilding . '">'
        ], false);
    }
}