<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class OrderGatewayService
{
    public function send(array $items): Response
    {
        return Http::withHeaders([
            'Authorization' => 'wQ8ehU2x4gj93CH9lMTnelQO3GcFvLzyqn8Fj3WA0ffQy57I60',
        ])->post('https://luvinco.proxy.beeceptor.com/orders', [
            'items' => collect($items)->map(function ($item) {
                return [
                    'product_id' => $item->product->product_id ?? null,
                    'quantity' => $item->quantity,
                ];
            })->values(),
        ]);
    }
}
