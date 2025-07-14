<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    public function index()
    {
        $order = $this->service->getLatestOpen();

        if (!$order) {
            return $this->error('Nenhum pedido em aberto encontrado.');
        }

        return $this->success($order, 'Pedido em aberto carregado com sucesso.');
    }

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
