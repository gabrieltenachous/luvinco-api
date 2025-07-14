<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_finalize_order_and_decrement_stock()
    {
        $product = Product::factory()->create(['stock' => 5]);

        $orderResponse = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 2]
            ]
        ]);

        $orderId = $orderResponse->json('data.id');

        $this->postJson('/api/order-products', [
            'order_id' => $orderId,
        ])->assertStatus(200)
          ->assertJsonPath('message', 'Pedido finalizado com sucesso.');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'finalizado',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
    }

    public function test_should_fail_to_finalize_already_closed_order()
    {
        $order = Order::factory()->create(['status' => 'finalizado']);

        $this->postJson('/api/order-products', [
            'order_id' => $order->id,
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['order_id']);
    }

    public function test_should_fail_if_insufficient_stock()
    {
        $product = Product::factory()->create(['stock' => 1]);

        $orderResponse = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 1]
            ]
        ]);

        $orderId = $orderResponse->json('data.id');

        // manualmente aumentar a quantidade no banco para simular inconsistência
        OrderProduct::where('order_id', $orderId)->update(['quantity' => 5]);

        $this->postJson('/api/order-products', [
            'order_id' => $orderId,
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['items']);
    }

    public function test_should_list_order_products_by_order_id()
    {
        $product = Product::factory()->create(['stock' => 3]);

        $orderResponse = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 2]
            ]
        ]);

        $orderId = $orderResponse->json('data.id');

        $this->postJson('/api/order-products', ['order_id' => $orderId]);

        $response = $this->getJson('/api/order-products?order_id=' . $orderId);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Itens do pedido retornados com sucesso.')
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'order_id', 'product_id', 'quantity', 'unit_price', 'created_at']
                ]
            ]);
    }
}
