<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // ID:1　会員登録機能
    // 名前が入力されていない場合、バリデーションメッセージが表示される
    public function testNameIsRequiredForRegistration()
    {
        $response = $this->post('/register', [
            'email' => 'registration_test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('name');
        $response->assertSee('お名前を入力してください');
    }
}