<?php

namespace App\Http\Controllers;

use App\ApiResponder;

/**
 * @OA\Info(
 *     title="LuvinCo API",
 *     version="1.0.0",
 *     description="Documentação da API da loja virtual LuvinCo."
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor local"
 * )
 */
abstract class Controller
{
    use ApiResponder;
}
