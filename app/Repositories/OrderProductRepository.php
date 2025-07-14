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
    public function findByOrderAndProduct(string $orderId, string $productId): ?\App\Models\OrderProduct
    {
        return OrderProduct::where('order_id', $orderId)
            ->where('product_id', $productId)
            ->first();
    }

}
