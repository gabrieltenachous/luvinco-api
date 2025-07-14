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

    private OrderProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderProductService::class);
    }

    #[Test]
    public function it_should_finalize_order_and_decrement_stock(): void
    {
        $product = Product::factory()->create(['stock' => 15, 'price' => 12]);
        $order   = Order::factory()->create(['status' => 'aberto']);

        $order->orderProducts()->create([
            'product_id' => $product->id,
            'quantity'   => 4,
            'unit_price' => $product->price,
        ]);

        $items = $this->service->finalizeOrder($order->id);

        $this->assertCount(1, $items);
        $this->assertEquals('finalizado', $order->fresh()->status);
        $this->assertEquals(11, $product->fresh()->stock); // 15 - 4
    }

    #[Test]
    public function it_should_throw_validation_exception_if_order_not_found(): void
    {
        $this->expectException(ValidationException::class);
        $this->service->finalizeOrder('non-existing-uuid');
    }
}