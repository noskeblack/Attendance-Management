<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals('メールアドレスを入力してください', session('errors')->get('email')[0]);
    }

    public function test_login_requires_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'a@example.com',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals('パスワードを入力してください', session('errors')->get('password')[0]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password12345'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals('ログイン情報が登録されていません', session('errors')->get('email')[0]);
    }
}
