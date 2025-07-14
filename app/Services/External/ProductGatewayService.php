<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class ProductGatewayService
{
    public function fetch(): Collection
    {
        $response = Http::withHeaders([
            'Authorization' => 'wQ8ehU2x4gj93CH9lMTnelQO3GcFvLzyqn8Fj3WA0ffQy57I60',
        ])->get('https://luvinco.proxy.beeceptor.com/products');

        if (! $response->successful()) {
            throw new \Exception('Erro ao buscar produtos externos.');
        }

        return collect($response->json());
    }
}