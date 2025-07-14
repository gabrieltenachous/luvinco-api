<?php
namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    #[Test]
    public function should_create_order_with_valid_products(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $order = $this->service->createWithItems([
            ['product_id' => $product->product_id, 'quantity' => 2],
        ]);

        $this->assertEquals('aberto', $order->status);
    }


    #[Test]
    public function should_fail_if_product_does_not_exist(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createWithItems([
            ['product_id' => 'non-existent', 'quantity' => 1]
        ]);
    }

    #[Test]
    public function should_fail_if_stock_is_insufficient(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $this->expectException(ValidationException::class);

        $this->service->createWithItems([
            ['product_id' => $product->product_id, 'quantity' => 5]
        ]);
    }
}
