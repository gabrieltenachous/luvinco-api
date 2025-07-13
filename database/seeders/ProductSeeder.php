<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Product; 

class ProductSeeder extends Seeder
{
    public function run(): void
    { 

        $response = Http::withHeaders([
            'Authorization' => 'wQ8ehU2x4gj93CH9lMTnelQO3GcFvLzyqn8Fj3WA0ffQy57I60',
        ])->get('https://luvinco.proxy.beeceptor.com/products');

        if ($response->successful()) {
            foreach ($response->json() as $item) {
                Product::updateOrCreate(
                    ['product_id' => $item['product_id']],
                    [
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'category' => $item['category'],
                        'brand' => $item['brand'],
                        'stock' => $item['stock'],
                        'image_url' => 'https://placehold.co/600x600?text=' . urlencode($item['name']) . '&font=roboto',
                    ]
                );
            }

            $this->command->info('✅ Produtos importados com imagens fake.');
        } else {
            $this->command->error('❌ Falha ao importar produtos da API externa.');
        }
    }
}
