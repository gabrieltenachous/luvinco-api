<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'clear' => 'sometimes|boolean',
            'items' => 'required_unless:clear,true|array',
            'items.*.product_id' => 'required|string|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
