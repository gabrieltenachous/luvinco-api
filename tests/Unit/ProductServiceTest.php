<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProductService::class);
    }

    #[Test]
    public function should_return_all_products()
    {
        Product::factory()->count(5)->create();

        $result = $this->service->list([]);

        $this->assertCount(5, $result->resource->items());
    }

    #[Test]
    public function should_filter_products_by_name()
    {
        Product::factory()->create(['name' => 'Camisa Polo Masculina']);
        Product::factory()->create(['name' => 'Relógio Smart']);

        $result = $this->service->list(['name' => 'Camisa']);

        $this->assertCount(1, $result->resource->items());
        $this->assertEquals('Camisa Polo Masculina', $result->resource->items()[0]->name);
    }
}
