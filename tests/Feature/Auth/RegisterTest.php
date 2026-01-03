<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // 名前が未入力の場合、バリデーションメッセージが表示される
    public function test_name_is_required(): void
    {
        $registerInput = $this->getValidRegisterInput([
            'name' => '',
        ]);

        $response = $this->post('/register', $registerInput);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);

        $this->assertGuest();
    }

    // メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_email_is_required(): void
    {
        $registerInput = $this->getValidRegisterInput([
            'email' => '',
        ]);

        $response = $this->post('/register', $registerInput);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);

        $this->assertGuest();
    }

    // パスワードが8文字未満の場合、バリデーションメッセージが表示される
    public function test_password_must_be_at_least_8_characters(): void
    {
        $registerInput = $this->getValidRegisterInput([
            'password' => 'passwor',
            'password_confirmation' => 'passwor',
        ]);

        $response = $this->post('/register', $registerInput);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        $this->assertGuest();
    }

    //パスワードが一致しない場合、バリデーションメッセージが表示される
    public function test_password_confirmation_mismatch(): void
    {
        $registerInput = $this->getValidRegisterInput([
            'password' => 'password',
            'password_confirmation' => 'passwordd',
        ]);

        $response = $this->post('/register', $registerInput);

        $response->assertSessionHasErrors([
            'password_confirmation' => 'パスワードと一致しません',
        ]);

        $this->assertGuest();
    }

    // パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_password_is_required(): void
    {
        $registerInput = $this->getValidRegisterInput([
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response = $this->post('/register', $registerInput);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);

        $this->assertGuest();
    }

    // フォームに内容が入力されていた場合、データが正常に保存される
    public function test_register_succeeds_and_user_is_saved(): void
    {
        $registerInput = $this->getValidRegisterInput();

        $response =$this->post('/register', $registerInput);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => $registerInput['name'],
            'email' => $registerInput['email'],
        ]);

        $user = User::where('email', $registerInput['email'])->first();

        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        $response->assertRedirect(route('register.verify'));
    }

    private function getValidRegisterInput(array $overrideInput = []): array
    {
        $validRegisterInput = [
            'name' => '西 伶奈',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        return array_merge($validRegisterInput, $overrideInput);
    }
}
