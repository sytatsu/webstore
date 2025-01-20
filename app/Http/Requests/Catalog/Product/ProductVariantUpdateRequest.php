<?php

namespace App\Http\Requests\Catalog\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            'name'        => ['string', 'max:100'],
            'description' => ['string'],
            'price'       => ['required', 'string'],
            'sku'         => ['required', 'string'],
            'variants'    => ['required', 'array'],
            'variants.*'  => ['string', 'exists:variants,uuid'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validatedData = parent::validated($key, $default);

        if ($key !== null) {
            return $validatedData;
        }

        return [
            'name'        => $validatedData['name'],
            'description' => $validatedData['description'],
            'price'       => $validatedData['price'],
            'sku'         => $validatedData['sku'],
            'variants'    => $validatedData['variants'],
        ];
    }
}
