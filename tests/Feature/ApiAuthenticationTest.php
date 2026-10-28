<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_routes_require_a_bearer_token(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();
        $this->getJson('/api/currencies')->assertUnauthorized();
        $this->postJson('/api/products', [])->assertUnauthorized();
    }

    public function test_user_can_get_and_revoke_a_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'feature-test',
        ])->assertOk()->assertJsonStructure([
            'token',
            'token_type',
            'expires_at',
        ]);

        $token = $response->json('token');
        $this->withToken($token)->getJson('/api/products')->assertOk();

        $this->withToken($token)->deleteJson('/api/auth/token')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/products')->assertUnauthorized();
    }

    public function test_invalid_credentials_do_not_issue_a_token(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'incorrect',
        ])->assertUnauthorized()->assertJsonMissingPath('token');
    }

    public function test_token_expires_after_the_configured_time(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('expiration-test', ['*'], now()->addMinutes(60));

        $this->travel(61)->minutes();

        $this->withToken($token->plainTextToken)
            ->getJson('/api/products')
            ->assertUnauthorized();
    }

    public function test_token_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->postJson('/api/auth/token', [
                'email' => 'missing@example.com',
                'password' => 'incorrect',
            ])->assertUnauthorized();
        }

        $this->postJson('/api/auth/token', [
            'email' => 'missing@example.com',
            'password' => 'incorrect',
        ])->assertTooManyRequests();
    }
}
