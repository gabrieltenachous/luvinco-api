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

}
