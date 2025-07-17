<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function should_create_order_with_products()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->withHeaders([
            'Authorization' => env('API_CUSTOM_TOKEN')
        ])->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 2],
            ]
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'status', 'created_at'],
            ])
            ->assertJsonPath('message', 'Itens adicionados ao carrinho com sucesso.')
            ->assertJsonPath('data.status', 'aberto');
    }

    #[Test]
    public function should_fail_if_product_stock_is_insufficient(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $response = $this->withHeaders([
            'Authorization' => env('API_CUSTOM_TOKEN')
        ])->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 5],
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('stock');
    }

    #[Test]
    public function should_list_latest_open_order()
    {
        $product = Product::factory()->create(['stock' => 3]);
 
        $this->withHeaders([
            'Authorization' => env('API_CUSTOM_TOKEN')
        ])->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 2],
            ]
        ]);

        $response = $this->getJson('/api/orders');

        $response->assertOk()
            ->assertJsonPath('data.status', 'aberto')
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'status', 'created_at'],
            ]);
    }
}
