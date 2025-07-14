<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Services\External\ProductGatewayService;

class ProductSeeder extends Seeder
{
    public function __construct(private ProductGatewayService $gateway) {}
    
    public function run(): void
    { 
        $products = $this->gateway->fetch(); 
        foreach ($products as $item) {
            Product::updateOrCreate(
                ['product_id' => $item['product_id']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'category' => $item['category'],
                    'brand' => $item['brand'],
                    'stock' => $item['stock'] ?? 10,
                    'image_url' => $item['image_url'] ?? 'https://placehold.co/600x600?text=Product',
                ]
            );
        }

        $this->command->info('✅ Produtos importados com imagens fake.'); 
    }
}
