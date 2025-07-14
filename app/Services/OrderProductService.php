<?php

namespace App\Services;

use App\Repositories\OrderProductRepository;
use App\Http\Resources\OrderProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderProductService
{
    public function __construct(private OrderProductRepository $repository) {}

    public function listByOrder(string $orderId): AnonymousResourceCollection
    {
        return OrderProductResource::collection(
            $this->repository->getByOrder($orderId)
        );
    }

    public function storeItems(string $orderId, array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = new OrderProductResource(
                $this->repository->create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ])
            );
        }

        return $result;
    }
}
