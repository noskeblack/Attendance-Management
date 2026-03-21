<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_requires_name(): void
    {
        $response = $this->post('/register', [
            'email' => 'a@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals('お名前を入力してください', session('errors')->get('name')[0]);
    }


    public function test_register_requires_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame('メールアドレスを入力してください', session('errors')->first('email'));
    }

    public function test_register_requires_password_confirmation_match(): void
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'a@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertSame('パスワードと一致しません', session('errors')->first('password'));
    }
    public function test_register_requires_password_min_8(): void
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'a@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
