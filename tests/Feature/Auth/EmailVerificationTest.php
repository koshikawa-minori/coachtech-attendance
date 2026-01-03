<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function test_verification_email_is_sent_after_registration(): void
    {
        // TODO: 会員登録をPOST
        // TODO: Notification::assertSentTo($user, VerifyEmail::class)
        $this->assertTrue(true);
    }

    public function test_user_can_open_verification_prompt_and_go_to_verification_site(): void
    {
        // TODO: 認証誘導画面を表示
        // TODO: 「認証はこちらから」のリンク先確認
        $this->assertTrue(true);
    }

    public function test_user_is_redirected_to_attendance_screen_after_email_verification(): void
    {
        // TODO: 未認証ユーザーを作る
        // TODO: 署名付きURLで認証ルートへGET
        // TODO: email_verified_at が埋まる & 勤怠登録画面へ遷移
        $this->assertTrue(true);
    }
}
