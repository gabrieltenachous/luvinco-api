<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductService
{
    public function __construct(private ProductRepository $repository) {}

    public function list(array $filters = []): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->repository->filter($filters)
        );
    }
}
