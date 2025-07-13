<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API Luvinco",
 *     version="1.0",
 *     description="Documentação da API da vitrine de produtos"
 * )
 *
 * @OA\Get(
 *     path="/api/products",
 *     summary="Listar produtos",
 *     tags={"Produtos"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de produtos retornada com sucesso"
 *     )
 * )
 */
class ProductController extends Controller
{
    public function index()
    {
        // Lógica do endpoint
    }
}
