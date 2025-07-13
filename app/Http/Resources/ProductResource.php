<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'brand' => $this->brand,
            'stock' => $this->stock,
            'image_url' => $this->image_url,
        ];
    }
}
