<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function getLatestOpen(): ?Order
    {
        return Order::where('status', 'aberto')
            ->latest()
            ->first();
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
}
