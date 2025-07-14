<?php

namespace App\Repositories;

use App\Models\OrderProduct;

class OrderProductRepository
{
    public function create(array $data): OrderProduct
    {
        return OrderProduct::create($data);
    }

    public function getByOrder(string $orderId)
    {
        return OrderProduct::where('order_id', $orderId)->get();
    }
}
