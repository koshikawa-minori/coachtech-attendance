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

    private User $registeredUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registeredUser = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_login_fails_when_email_is_missing(): void
    {
        // TODO: /login にPOST
        // TODO: assertSessionHasErrors('email')
        $this->assertTrue(true);
    }

    public function test_login_fails_when_password_is_missing(): void
    {
        // TODO: assertSessionHasErrors('password')
        $this->assertTrue(true);
    }

    public function test_login_fails_when_credentials_do_not_match(): void
    {
        // TODO: 誤った資格情報でPOST
        // TODO: エラー文言 or errors を確認
        $this->assertTrue(true);
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        // TODO: $this->registeredUser のメール + password123 でPOST
        // TODO: assertAuthenticatedAs($this->registeredUser)
        $this->assertTrue(true);
    }
}
