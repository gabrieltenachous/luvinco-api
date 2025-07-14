<?php

namespace App\Services;

use App\Repositories\OrderProductRepository;
use App\Http\Resources\OrderProductResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class OrderProductService
{
    public function __construct(private OrderProductRepository $repository) {}

    public function listByOrder(string $orderId): AnonymousResourceCollection
    {
        return OrderProductResource::collection(
            $this->repository->getByOrder($orderId)
        );
    }
    public function listCompletedOrders(): AnonymousResourceCollection
    {
        return OrderProductResource::collection(
            $this->repository->getCompletedOrders()
        );
    }
    public function finalizeOrder(string $orderId): array
    {
        $order = Order::where('id', $orderId)
            ->where('status', 'aberto')
            ->with('orderProducts')
            ->first();

        if (!$order) {
            throw ValidationException::withMessages([
                'order_id' => ['Invalid or already closed order.']
            ]);
        }

        $result = [];

        foreach ($order->orderProducts as $pivot) {
            $product = $pivot->product;

            if ($product->stock < $pivot->quantity) {
                throw ValidationException::withMessages([
                    'items' => ["Product '{$product->name}' acabou o estoque."]
                ]);
            }
            $product->decrement('stock', $pivot->quantity);
            $result[] = new OrderProductResource($pivot);
        }
        $order->update(['status' => 'finalizado']);
        return $result;
    }


    public function addOrUpdateItems(Order $order, array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $product = Product::where('product_id', $item['product_id'])->first();

            if (!$product || $product->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => ["Produto '{$product?->name}' acabou o estoque."]
                ]);
            }

            $existing = $this->repository->findByOrderAndProduct($order->id, $product->id);

            if ($existing) {
                $existing->update([
                    'quantity' => $existing->quantity + $item['quantity'],
                    'unit_price' => $product->price,
                ]);
                $result[] = new OrderProductResource($existing);
            } else {
                $new = $this->repository->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
                $result[] = new OrderProductResource($new);
            }
        }

        return $result;
    }


}
