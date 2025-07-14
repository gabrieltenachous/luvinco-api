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

    public function messages(): array
    {
        return [
            'clear.boolean' => 'O campo "clear" deve ser verdadeiro ou falso.',
            'items.required_unless' => 'A lista de itens é obrigatória, a menos que o campo "clear" seja verdadeiro.',
            'items.array' => 'O campo "items" deve ser um array.',
            'items.*.product_id.required' => 'O campo "product_id" de cada item é obrigatório.',
            'items.*.product_id.string' => 'O campo "product_id" deve ser uma string.',
            'items.*.product_id.exists' => 'O produto informado não foi encontrado.',
            'items.*.quantity.required' => 'O campo "quantity" de cada item é obrigatório.',
            'items.*.quantity.integer' => 'O campo "quantity" deve ser um número inteiro.',
            'items.*.quantity.min' => 'A quantidade mínima permitida é 1.',
        ];
    }

}
