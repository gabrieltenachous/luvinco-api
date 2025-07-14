<?php

namespace App\Http\Controllers;

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
}
