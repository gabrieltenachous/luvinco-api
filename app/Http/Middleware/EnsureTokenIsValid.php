<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization'); 
        $expected = config('auth.custom_token'); 
        if (!$token || $token !== $expected) {
            return response()->json([
                'message' => 'Token de autorização inválido.'
            ], 401);
        }

        return $next($request);
    }
}
