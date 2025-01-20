<?php

namespace App\Http\Requests\Catalog\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductContinueRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'exists:products,uuid'],
        ];
    }
}
