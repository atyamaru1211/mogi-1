<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    // ID:3 ログアウト機能
    // ログアウトができる
    public function testUserCanLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest(); 
    }
}