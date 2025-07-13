<?php

namespace App;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponder
{
    public function success($data, string $message = 'Sucesso', int $status = 200): JsonResponse|JsonResource
    {
        if ($data instanceof ResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            return response()->json([
                'message' => $message,
                'data' => $data->resource->items(), 
                'links' => [
                    'first' => $data->resource->url(1),
                    'last' => $data->resource->url($data->resource->lastPage()),
                    'prev' => $data->resource->previousPageUrl(),
                    'next' => $data->resource->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $data->resource->currentPage(),
                    'last_page' => $data->resource->lastPage(),
                    'per_page' => $data->resource->perPage(),
                    'total' => $data->resource->total(),
                ],
            ], $status);
        }
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}