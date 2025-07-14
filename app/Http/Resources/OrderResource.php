<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    { 
        return [
            'id' => $this->id, 
            'status' => $this->status,
            'order_products' => OrderProductResource::collection($this->whenLoaded('orderProducts')),
            'created_at' => $this->created_at,
        ];
    }
}
