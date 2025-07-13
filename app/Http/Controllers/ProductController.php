<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Http\Resources\StandardResource;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{ 
    public function __construct(private ProductService $service) {}

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar produtos com filtros",
     *     tags={"Produtos"},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nome", @OA\Schema(type="string")),
     *     @OA\Parameter(name="brand", in="query", description="Filtrar por marca", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category", in="query", description="Filtrar por categoria", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Lista de produtos")
     * )
     */
    public function index(Request $request)
    {
        $products = $this->service->list($request->only(['name', 'brand', 'category']));
        return $this->success($products, 'Produtos listados com sucesso.');
    }
}
