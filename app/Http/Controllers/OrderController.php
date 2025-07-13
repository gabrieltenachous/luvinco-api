<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        $order = $this->service->getOrCreateOpenOrder($sessionId);

        return $this->success($order, 'Pedido atual carregado com sucesso.');
    }
}
