<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderProductService::class);
    }

    #[Test]
    public function should_finalize_order_and_decrement_stock()
    {
        $product = Product::factory()->create(['stock' => 5]);
        $order = Order::factory()->create(['status' => 'aberto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => $product->price,
        ]);

        $this->service->finalizeOrder($order->id); 
        $this->assertEquals(2, $product->fresh()->stock);
        $this->assertEquals('finalizado', $order->fresh()->status);
    }

    #[Test]
    public function should_fail_to_finalize_if_insufficient_stock()
    {
        $product = Product::factory()->create(['stock' => 2]);
        $order = Order::factory()->create(['status' => 'aberto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => $product->price,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->finalizeOrder($order->id);
    }
}
