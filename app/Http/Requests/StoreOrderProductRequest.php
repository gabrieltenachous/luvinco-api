<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => 'required|uuid|exists:orders,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
    
    public function messages(): array
    {
        return [
            'order_id.required' => 'O campo "order_id" é obrigatório.',
            'order_id.uuid' => 'O campo "order_id" deve ser um UUID válido.',
            'order_id.exists' => 'O pedido informado não foi encontrado.',
        ];
    }

}
