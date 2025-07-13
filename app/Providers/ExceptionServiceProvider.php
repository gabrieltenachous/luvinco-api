<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException; 
use Throwable;

class ExceptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, function () {
            return new class extends \Illuminate\Foundation\Exceptions\Handler {
                public function render($request, Throwable $e)
                {
                    if (!$request->expectsJson()) {
                        return parent::render($request, $e);
                    }

                    if ($e instanceof ValidationException) {
                        return response()->json([
                            'message' => 'Erro de validação.',
                            'errors' => $e->errors(),
                        ], 422);
                    }

                    if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                        return response()->json([
                            'message' => 'Recurso não encontrado.'
                        ], 404);
                    }

                    if ($e instanceof MethodNotAllowedHttpException) {
                        return response()->json([
                            'message' => 'Método não permitido.'
                        ], 405);
                    }

                    return response()->json([
                        'message' => 'Erro interno do servidor.',
                        'exception' => config('app.debug') ? $e->getMessage() : null
                    ], 500);
                }
            };
        });
    }
}
