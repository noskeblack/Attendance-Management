<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_requires_email(): void
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame('メールアドレスを入力してください', session('errors')->first('email'));
    }

    public function test_admin_login_requires_password(): void
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@example.com',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertSame('パスワードを入力してください', session('errors')->first('password'));
    }

    public function test_admin_login_rejects_general_user(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password12345'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'user@example.com',
            'password' => 'password12345',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame('ログイン情報が登録されていません', session('errors')->first('email'));
    }
}
