<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    // ID:2 ログイン機能
    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function testEmailIsRequiredForLogin()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'password' => 'password',
                'remember' => 'false',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $response->assertInvalid(['email' => 'メールアドレスを入力してください']);
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function testPsswordIsRequiredForLogin()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'logintest@example.com',
                'remember' => 'false',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('password');
        $response->assertInvalid(['password' => 'パスワードを入力してください']);
    }

    // 入力情報が間違っている場合、バリデーションメッセージが表示される
    public function testLoginFailsWithInvalidCredentials()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword',
                'remember' => 'false',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $response->assertInvalid(['email' => 'ログイン情報が登録されていません']);
    }

    // 正しい情報が入力された場合、ログイン処理が実行される
    public function testSuccessfulLogin()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'logintest@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'logintest@example.com',
                'password' => 'password',
                'remember' => 'true',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}