<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class OrderProductApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_finalize_an_order_successfully(): void
    {
        // Arrange
        $product = Product::factory()->create(['stock' => 10, 'price' => 99.90]);
        $order   = Order::factory()->create(['status' => 'aberto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => $product->price,
        ]);

        // Mock da API externa
        Http::fake([
            'https://luvinco.proxy.beeceptor.com/orders' => Http::response([
                'message'            => 'Pedido recebido com sucesso.',
                'estimated_delivery' => '2025-07-20',
            ], 200),
        ]);

        // Act
        $response = $this->postJson(
            '/api/order-products',
            ['order_id' => $order->id]
        );

        // Assert
        $response->assertOk()
                 ->assertJsonPath('data.mensagem', 'Pedido recebido com sucesso.')
                 ->assertJsonPath('data.entrega',  '2025-07-20');

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'finalizado',
        ]);

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'stock' => 8, // 10 - 2
        ]);
    }

    #[Test]
    public function it_should_fail_if_external_api_returns_error(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'price' => 55]);
        $order   = Order::factory()->create(['status' => 'aberto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity'   => 3,
            'unit_price' => $product->price,
        ]);

        Http::fake([
            'https://luvinco.proxy.beeceptor.com/orders' =>
                Http::response(['message' => 'Erro externo.'], 400),
        ]);

        $response = $this->postJson(
            '/api/order-products',
            ['order_id' => $order->id]
        );

        $response->assertStatus(500)
                 ->assertJson(['message' =>
                     'Erro ao integrar com o sistema externo. Nenhuma alteração foi salva.']);

        // Pedido permanece aberto
        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'aberto',
        ]);

        // Estoque não alterado
        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'stock' => 5,
        ]);
    }
}