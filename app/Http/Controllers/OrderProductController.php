<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderProductRequest;
use Illuminate\Http\Request;
use App\Services\OrderProductService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class OrderProductController extends Controller
{
    public function __construct(private OrderProductService $service) {}

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
    public function store(StoreOrderProductRequest $request)
    {
        try {
            $response = DB::transaction(function () use ($request) {
                // Finaliza o pedido (atualiza status e desconta estoque)
                $items = $this->service->finalizeOrder($request->order_id);

                // Envia para o endpoint externo (somente product_id e quantity)
                $res =  Http::withHeaders([
                    'Authorization' => 'wQ8ehU2x4gj93CH9lMTnelQO3GcFvLzyqn8Fj3WA0ffQy57I60',
                ])->post('https://luvinco.proxy.beeceptor.com/orders', [
                    'items' => collect($items)->map(function ($item) {
                        return [
                            'product_id' => $item->product->product_id ?? null,
                            'quantity' => $item->quantity,
                        ];
                    })->values(), // remove chaves associativas se houver
                ]); 
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
            return $this->error(
                'Erro ao integrar com o sistema externo. Nenhuma alteração foi salva.',
                500
            );
        }
    }
}
