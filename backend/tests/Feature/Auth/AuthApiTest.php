<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_cookie_endpoint_returns_204(): void
    {
        // Sanctum SPA clients call this endpoint before sending state-changing requests.
        $this->get('/sanctum/csrf-cookie')->assertNoContent();
    }

    public function test_user_can_login_and_fetch_the_authenticated_user(): void
    {
        $user = UserFactory::new()->create([
            'email' => 'gardener@example.com',
            'password' => 'secret-password',
        ]);

        $this->withHeaders($this->statefulHeaders())->postJson('/api/auth/login', [
            'email' => 'gardener@example.com',
            'password' => 'secret-password',
        ])->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', 'gardener@example.com')
            ->assertJsonMissingPath('password');

        $this->withHeaders($this->statefulHeaders())->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', 'gardener@example.com');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        UserFactory::new()->create([
            'email' => 'gardener@example.com',
            'password' => 'secret-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'gardener@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();

        $this->assertGuest();
    }

    public function test_login_validates_email_and_password(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = UserFactory::new()->create();

        $this->withHeaders($this->statefulHeaders())->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

        $this->withHeaders($this->statefulHeaders())
            ->postJson('/api/auth/logout')
            ->assertNoContent();

        // Each production request resolves a fresh Sanctum request guard. The
        // feature-test application instance is shared, so clear its cached user.
        $this->app['auth']->forgetGuards();

        $this->assertGuest();
        $this->withHeaders($this->statefulHeaders())
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_fetch_me_or_logout(): void
    {
        $this->withHeaders($this->statefulHeaders())
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
        $this->withHeaders($this->statefulHeaders())
            ->postJson('/api/auth/logout')
            ->assertUnauthorized();
    }

    /** @return array<string, string> */
    private function statefulHeaders(): array
    {
        return [
            'Origin' => 'http://localhost:5173',
            'Referer' => 'http://localhost:5173',
        ];
    }
}
