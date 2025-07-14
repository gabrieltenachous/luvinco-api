<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function should_return_paginated_products_structure()
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/products'); 
        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    #[Test]
    public function should_filter_products_by_name()
    {
        Product::factory()->create(['name' => 'Tênis Masculino Adidas']);
        Product::factory()->create(['name' => 'Relógio Apple']);

        $response = $this->getJson('/api/products?name=T%C3%AAnis');

        $response->assertOk();
        $this->assertCount(1, $response['data']);
        $this->assertEquals('Tênis Masculino Adidas', $response['data'][0]['name']);
    }

    #[Test]
    public function should_filter_by_brand_and_category()
    {
        Product::factory()->create([
            'name' => 'iPhone 13',
            'brand' => 'Apple',
            'category' => 'Eletrônicos',
        ]);

        Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'brand' => 'Samsung',
            'category' => 'Celulares',
        ]);

        $response = $this->getJson('/api/products?brand=Apple&category=Eletrônicos');

        $response->assertOk();
        $this->assertCount(1, $response['data']);
        $this->assertEquals('iPhone 13', $response['data'][0]['name']);
    }
}
