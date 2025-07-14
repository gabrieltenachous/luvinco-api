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
    public function getCompletedOrders(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\OrderProduct::with('product', 'order')
            ->whereHas('order', fn($q) => $q->where('status', 'finalizado'))
            ->latest()
            ->get();
    }

}
