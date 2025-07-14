<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest; 
use App\Services\OrderService; 

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Obter pedido em aberto",
     *     tags={"Pedidos"},
     *     @OA\Response(
     *         response=200,
     *         description="Pedido em aberto carregado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum pedido em aberto encontrado"
     *     )
     * )
     */
    public function getOpenOrder()
    {
        $order = $this->service->getLatestOpen();

        if (!$order) {
            return $this->error('Nenhum pedido em aberto encontrado.');
        }

        return $this->success($order, 'Pedido em aberto carregado com sucesso.');
    }
    /**
     * @OA\Get(
     *     path="/api/orders/completed",
     *     summary="Listar pedidos finalizados",
     *     tags={"Pedidos"},
     *     @OA\Response(
     *         response=200,
     *         description="Pedidos concluídos carregados com sucesso"
     *     )
     * )
     */
    public function listCompletedOrders()
    {
        $orders = $this->service->listCompleted();
        return $this->success($orders, 'Pedidos concluídos carregados com sucesso.');
    }
     /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Adicionar itens ao carrinho",
     *     tags={"Pedidos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(property="clear", type="boolean", example=true),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="string", example="12345"),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Itens adicionados ao carrinho com sucesso"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $order = $this->service->createWithItems(
            $data['items'] ?? [],  
            $data['clear'] ?? false
        );
        return $this->success($order, 'Itens adicionados ao carrinho com sucesso.');
    }

}
