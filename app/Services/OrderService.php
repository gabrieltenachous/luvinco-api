<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;
use App\Models\Product; 
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private OrderRepository $repository) {}

    public function getLatestOpen(): ?OrderResource
    {
        $order = $this->repository->getLatestOpen();

        return $order ? new OrderResource($order) : null;
    }
    public function createWithItems(array $items): OrderResource
    {
        $order = $this->repository->create(['status' => 'aberto']);

        foreach ($items as $item) {
            $product = Product::where('product_id', $item['product_id'])->first();

            if (!$product || $product->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => ["Produto '{$product?->name}' sem estoque suficiente."]
                ]);
            }

            // Aqui, salvar relação virá no commit 12 (OrderProduct)
        }

        return new OrderResource($order);
    }
}
