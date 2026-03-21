<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_sends_verification_notification(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => '新規ユーザー',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $user = User::query()->where('email', 'newuser@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verify_email_notice_contains_signed_verification_link_button(): void
    {
        $user = User::factory()->unverified()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)
            ->get('/email/verify')
            ->assertOk()
            ->assertSee('認証はこちらから', false);

        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/href="([^"]*email\\/verify\\/[^"]+)"/', $content);
        preg_match('/href="([^"]*email\\/verify\\/[^"]+)"/', $content, $m);
        $href = html_entity_decode($m[1]);

        parse_str(parse_url($href, PHP_URL_QUERY) ?? '', $query);
        $this->assertArrayHasKey('expires', $query);
        $this->assertArrayHasKey('signature', $query);
    }

    public function test_visiting_signed_verification_url_verifies_and_redirects_to_attendance(): void
    {
        $user = User::factory()->unverified()->create([
            'is_admin' => false,
        ]);

        $this->assertNull($user->email_verified_at);

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $this->actingAs($user)
            ->get($verifyUrl)
            ->assertRedirect(route('attendance.index'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }
}
