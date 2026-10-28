<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_products_currencies_and_prices(): void
    {
        $user = User::factory()->create();
        $token = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $currencyId = $this->withToken($token)->postJson('/api/currencies', [
            'name' => 'Peso argentino',
            'symbol' => 'ARS',
            'exchange_rate' => 1,
        ])->assertCreated()->json('id');

        $productId = $this->withToken($token)->postJson('/api/products', [
            'name' => 'Notebook',
            'description' => 'Producto de prueba',
            'price' => 1200,
            'currency_id' => $currencyId,
            'tax_cost' => 210,
            'manufacturing_cost' => 500,
        ])->assertCreated()->json('id');

        $this->withToken($token)->getJson('/api/products')
            ->assertOk()
            ->assertJsonPath('data.0.id', $productId);

        $this->withToken($token)->putJson("/api/products/{$productId}", [
            'name' => 'Notebook actualizada',
        ])->assertOk()->assertJsonPath('name', 'Notebook actualizada');

        $alternativeCurrencyId = $this->withToken($token)->postJson('/api/currencies', [
            'name' => 'Dólar de prueba',
            'symbol' => 'USD',
            'exchange_rate' => 1000,
        ])->assertCreated()->json('id');

        $this->withToken($token)->postJson("/api/products/{$productId}/prices", [
            'currency_id' => $alternativeCurrencyId,
            'price' => 1.25,
        ])->assertCreated();

        $this->withToken($token)->getJson("/api/products/{$productId}/prices")
            ->assertOk()
            ->assertJsonCount(1);

        $this->withToken($token)->deleteJson("/api/products/{$productId}")
            ->assertOk();
        $this->withToken($token)->getJson("/api/products/{$productId}")
            ->assertNotFound();
    }

    public function test_product_validation_rejects_negative_prices(): void
    {
        $user = User::factory()->create();
        $token = $this->postJson('/api/auth/token', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $this->withToken($token)->postJson('/api/products', [
            'name' => 'Producto inválido',
            'price' => -1,
            'currency_id' => 1,
            'tax_cost' => 0,
            'manufacturing_cost' => 0,
        ])->assertUnprocessable()->assertJsonValidationErrors('price');
    }
}
