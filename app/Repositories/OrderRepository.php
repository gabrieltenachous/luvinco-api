<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function getOpenBySession(string $sessionId): ?Order
    {
        return Order::where('session_id', $sessionId)
            ->where('status', 'aberto')
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
