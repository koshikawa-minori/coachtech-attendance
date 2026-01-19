<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

final class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'is_admin' => true,
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    // メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_admin_email_is_required(): void
    {
        $loginInput = $this->getValidLoginInput([
            'email' => '',
        ]);

        $response = $this->from('/admin/login')->post('/login', $loginInput);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    // パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_admin_password_is_required(): void
    {
        $loginInput = $this->getValidLoginInput([
            'password' => '',
        ]);

        $response = $this->from('/admin/login')->post('/login', $loginInput);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    // 登録内容と一致しない場合、バリデーションメッセージが表示される
    public function test_admin_input_information_error(): void
    {
        $loginInput = $this->getValidLoginInput([
            'email' => 'test1@example.com',
        ]);

        $response = $this->from('/admin/login')->post('/login', $loginInput);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);

        $this->get('/admin/login')->assertSeeText('ログイン情報が登録されていません');
    }

    private function getValidLoginInput(array $overrideInput = []): array
    {
        $validLoginInput = [
            'login_type' => 'admin',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        return array_merge($validLoginInput, $overrideInput);
    }
}
