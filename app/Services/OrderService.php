<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

class OrderService
{
    public function __construct(private OrderRepository $repository) {}

    public function getOrCreateOpenOrder(string $sessionId): OrderResource
    {
        $order = $this->repository->getOpenBySession($sessionId)
            ?? $this->repository->create(['session_id' => $sessionId]);

        return new OrderResource($order);
    }
}
