<?php

namespace App\Services;

use App\Repositories\OrderProductRepository;
use App\Http\Resources\OrderProductResource;
use App\Models\Order;
use App\Models\OrderProduct;
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
                'order_id' => ['Carrinho não encontrado ou já finalizado.']
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


    public function addOrUpdateItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $product = Product::where('product_id', $item['product_id'])->firstOrFail();
            $quantityToAdd = $item['quantity'];

            $existing = $order->orderProducts()
                ->where('product_id', $product->id)
                ->first();

            $currentQuantity = $existing?->quantity ?? 0;
            $totalRequested = $currentQuantity + $quantityToAdd;

            if ($totalRequested > $product->stock) {
                throw ValidationException::withMessages([
                    'stock' => "Quantidade solicitada para \"{$product->name}\" excede o estoque disponível ({$product->stock})."
                ]);
            }

            // Atualiza ou cria item do pedido
            OrderProduct::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $totalRequested,
                    'unit_price' => $product->price,
                ]
            );
        }
    }


}
