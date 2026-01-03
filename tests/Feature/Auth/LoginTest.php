<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    // メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_email_is_required(): void
    {
        $loginInput = $this->getValidLoginInput([
            'email' => '',
        ]);

        $response = $this->from('/login')->post('/login', $loginInput);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);

        $this->assertGuest();
    }

    // パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_password_is_required(): void
    {
        $loginInput = $this->getValidLoginInput([
            'password' => '',
        ]);

        $response = $this->from('/login')->post('/login', $loginInput);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);

        $this->assertGuest();
    }

    // 登録内容と一致しない場合、バリデーションメッセージが表示される
    public function test_input_information_error(): void
    {
        $loginInput = $this->getValidLoginInput([
            'email' => 'test1@example.com',
        ]);

        $response = $this->from('/login')->post('/login', $loginInput);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);

        $this->followRedirects($response)->assertSee('ログイン情報が登録されていません');

        $this->assertGuest();

    }

    private function getValidLoginInput(array $overrideInput = []): array
    {
        $validLoginInput = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        return array_merge($validLoginInput, $overrideInput);
    }
}
