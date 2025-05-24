<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // ID:1　会員登録機能
    // 名前が入力されていない場合、バリデーションメッセージが表示される
    public function testNameIsRequiredForRegistration()
    {
        $response = $this ->from('/register')
            ->post('/register', [
                'email' => 'registration_test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('name');
        $response->assertInvalid(['name' => 'お名前を入力してください']);
    }

    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function testEmailIsRequiredForRegistration()
    {
        $response = $this ->from('/register')
            ->post('/register', [
                'name' => 'テストユーザー',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $response->assertInvalid(['email' => 'メールアドレスを入力してください']);
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function testPasswordIsRequiredForRegistration()
    {
        $response = $this ->from('/register')
            ->post('/register', [
                'name' => 'テストユーザー',
                'email' => 'registration_test@example.com',
            ]);
        
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $response->assertInvalid(['password' => 'パスワードを入力してください']);
    }

    // パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function testPasswordIsTooShortForRegistration()
    {
        $response = $this ->from('/register')
            ->post('/register', [
                'name' => 'テストユーザー',
                'email' => 'registration_test@example.com',
                'password' => '1234567',
                'password_confirmation' => '1234567',
            ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $response->assertInvalid(['password' => 'パスワードは8文字以上で入力してください']);
    }

    // パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function testPasswordConfirmationDoesNotMatch()
    {
        $response = $this ->from('/register')
            ->post('/register', [
                'name' => 'テストユーザー',
                'email' => 'registration_test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'differentpassword',
            ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password_confirmation');
        $response->assertInvalid(['password_confirmation' => 'パスワードと一致しません']);
    }

    // ★全ての項目が入力されている場合、メール認証誘導画面へ遷移★　README参照
    public function testSuccessfulRegistrationAndRedirectionToEmailVerificationNotice()
    {
        $response = $this ->from('/register')
            ->post('/register', [
                'name' => '成功テストユーザー',
                'email' => 'successful_registration_test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'successful_registration_test@example.com',
        ]);

        $response->assertRedirect('/email/verify/notice');
    }
}
