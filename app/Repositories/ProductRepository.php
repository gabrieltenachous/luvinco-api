<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::all();
    }
    public function findProductId(string $id): ?Product
    {
        return Product::where('product_id', $id)->firstOrFail();
    }
    public function filter(array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Product::query()
            ->when($filters['name'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($filters['brand'] ?? null, fn($q, $v) => $q->where('brand', $v))
            ->when($filters['category'] ?? null, fn($q, $v) => $q->where('category', $v))
            ->paginate(10);
    }
}
