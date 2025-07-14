<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderProductRequest;
use Illuminate\Http\Request;
use App\Services\OrderProductService;

class OrderProductController extends Controller
{
    public function __construct(private OrderProductService $service) {}

    public function index(Request $request)
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
        $items = $this->service->finalizeOrder($request->order_id);

        return $this->success($items, 'Pedido finalizado com sucesso.');
    }
}
