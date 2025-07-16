<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function getLatestOpen(): ?Order
    {
        return Order::where('status', 'aberto')
            ->with('orderProducts.product')
            ->latest()
            ->first();
    }
    public function findIdStatusOpen($orderId): ?Order
    {
        return Order::where('id', $orderId)
            ->where('status', 'aberto')
            ->with('orderProducts')
            ->first();
    }
    public function paginateCompleted(): LengthAwarePaginator
    {
        return Order::with(['orderProducts.product'])
            ->where('status', 'finalizado') 
            ->paginate(5);
    }


    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order;
    }
    public function clearItems(Order $order): void
    {
        $order->orderProducts()->delete();
    }
    
}
