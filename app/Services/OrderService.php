<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderService
{
    public function __construct(
        private OrderRepository $repository,
        private OrderProductService $orderProductService,
    ) {}

    public function getLatestOpen(): ?OrderResource
    {
        $order = $this->repository->getLatestOpen();

        return $order ? new OrderResource($order) : null;
    }
    public function createWithItems(array $items, bool $clearCart = false): OrderResource
    {
        $order = $this->repository->getLatestOpen()
            ?? $this->repository->create(['status' => 'aberto']);

        if ($clearCart) {
            $this->repository->clearItems($order);
        }

        if (!empty($items)) {
            $this->orderProductService->addOrUpdateItems($order, $items);
        }

        return new OrderResource($order);
    }
    public function listCompleted(): AnonymousResourceCollection
    {
        return OrderResource::collection(
            $this->repository->paginateCompleted()
        );
    }
}
