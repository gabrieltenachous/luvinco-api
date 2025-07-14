<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrderRepository
{
    public function getLatestOpen(): ?Order
    {
        return Order::where('status', 'aberto')
            ->with('orderProducts.product')
            ->latest()
            ->first();
    }
    public function getCompleted(): Collection
    {
        return Order::with(['orderProducts.product'])
            ->where('status', 'finalizado')
            ->latest()
            ->get();
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
