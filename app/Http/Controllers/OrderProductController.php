<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderProductRequest;
use App\Services\External\OrderGatewayService;
use Illuminate\Http\Request;
use App\Services\OrderProductService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *     schema="OrderProduct",
 *     type="object",
 *     title="OrderProduct",
 *     required={"id", "order_id", "product_id", "quantity", "unit_price"},
 *     @OA\Property(property="id", type="string", format="uuid", example="0198085b-4e0a-722f-b133-72d55353fae7"),
 *     @OA\Property(property="order_id", type="string", format="uuid", example="01980829-cf70-7283-a16d-2c8374852322"),
 *     @OA\Property(property="product_id", type="string", format="uuid", example="019805f9-0e50-7293-974e-410641e1ff7a"),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price", type="number", format="float", example=79.99),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         @OA\Property(property="product_id", type="string", example="12345"),
 *         @OA\Property(property="name", type="string", example="Camisa Social Masculina"),
 *         @OA\Property(property="price", type="number", example=79.99),
 *         @OA\Property(property="category", type="string", example="Roupas Masculinas"),
 *         @OA\Property(property="brand", type="string", example="Zara"),
 *         @OA\Property(property="stock", type="integer", example=5),
 *         @OA\Property(property="image_url", type="string", format="url", example="https://placehold.co/600x600?text=Camisa+Social+Masculina")
 *     )
 * )
 */
class OrderProductController extends Controller
{
    public function __construct(
        private OrderProductService $service,
        private OrderGatewayService $gateway
        ) {}

    /**
     * @OA\Get(
     *     path="/api/order-products",
     *     security={{"apiToken":{}}},
     *     summary="Listar produtos de um pedido",
     *     tags={"OrderProducts"},
     *     @OA\Parameter(
     *         name="order_id",
     *         in="query",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Itens do pedido retornados com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Itens do pedido retornados com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/OrderProduct")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parâmetro obrigatório 'order_id' não enviado"
     *     )
     * )
     */
    public function listByOrder(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return $this->error('Parâmetro obrigatório "order_id" não enviado.', 400);
        }

        return $this->success(
            $this->service->listByOrder($orderId),
            'Itens do pedido retornados com sucesso.'
        );
    }
    
    /**
     * @OA\Post(
     *     path="/api/order-products",
     *     security={{"apiToken":{}}},
     *     summary="Finalizar pedido",
     *     tags={"OrderProducts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id"},
     *             @OA\Property(property="order_id", type="string", format="uuid", example="019809f8-61c1-7252-853b-58c0432fce2a")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido finalizado com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string", example="Pedido criado com sucesso."),
     *             @OA\Property(property="entrega", type="string", format="date-time"),
     *             @OA\Property(
     *                 property="pedido",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/OrderProduct")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao integrar com o sistema externo"
     *     )
     * )
     */
    public function store(StoreOrderProductRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                $items = $this->service->finalizeOrder($request->order_id);

                $res = $this->gateway->send($items);

                if ($res->status() !== 200) {
                    throw new \Exception($res->json()['message'] ?? 'Erro ao integrar pedido externo.');
                }

                return [
                    'beeceptor' => $res->json(),
                    'items' => $items,
                ];
            });

            return $this->success([
                'mensagem' => $response['beeceptor']['message'] ?? 'Pedido finalizado com sucesso.',
                'entrega' => $response['beeceptor']['estimated_delivery'] ?? null,
                'pedido' => $response['items'],
            ]);
        } catch (\Exception $e) {
            return $this->error('Erro ao integrar com o sistema externo. Nenhuma alteração foi salva.', 500);
        }
    }
}
