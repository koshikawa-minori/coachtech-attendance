<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;

final class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

    }

    // 会員登録後、認証メールが送信される
    public function test_verification_email_is_sent_after_registration(): void
    {
        $formData = [
            'name' => '西 伶奈',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $formData);
        $response->assertRedirect(route('register.verify'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $registeredUser = User::where('email', 'test@example.com')->firstOrFail();
        Notification::assertSentTo($registeredUser, VerifyEmail::class);

    }

    // メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証の案内画面に遷移する
    public function test_click_navigates_to_verification(): void
    {
        $formData = [
            'name' => '西 伶奈',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $formData);
        $response->assertRedirect(route('register.verify'));

        $registeredUser = User::where('email', 'test@example.com')->firstOrFail();

        $response = $this->actingAs($registeredUser)->get(route('verification.notice'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify');
    }

    // メール認証を完了すると、勤怠登録画面に遷移する
    public function test_redirects_to_attendance_after_email_verification(): void
    {
        $formData = [
            'name' => '西 伶奈',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $formData);
        $response->assertRedirect(route('register.verify'));

        $registeredUser = User::where('email', 'test@example.com')->firstOrFail();

        $verifyUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60),
            [
                'id' => $registeredUser->id,
                'hash' => sha1($registeredUser->email)
            ]
        );

        $response = $this->actingAs($registeredUser)->get($verifyUrl);

        $response->assertRedirect(route('attendance.show'));

        $this->assertTrue($registeredUser->fresh()->hasVerifiedEmail());
    }
}
