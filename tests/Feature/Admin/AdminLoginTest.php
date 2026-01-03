<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            // TODO: 管理者フラグのカラム名に合わせて修正（例: is_admin）
            'is_admin' => true,
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_admin_login_fails_when_email_is_missing(): void
    {
        // TODO: 管理者ログインURLにPOST（例: /admin/login）
        $this->assertTrue(true);
    }

    public function test_admin_login_fails_when_password_is_missing(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_login_fails_when_credentials_do_not_match(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_login_succeeds_with_correct_credentials(): void
    {
        // TODO: admin email + password123 でPOST
        // TODO: 管理画面へのリダイレクト等を確認
        $this->assertTrue(true);
    }
}
