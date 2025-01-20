<?php

namespace App\Http\Requests\Catalog\Product;

use App\Enums\AvailabilityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariantStoreRequest extends FormRequest
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

            'availability_type'     => ['required', 'string', Rule::enum(AvailabilityEnum::class)],
            'availability_location' => ['required', 'string', 'exists:availability_locations,label'],
            'availability_quantity' => ['required', 'numeric', 'min:0'],
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

            'availability' => [
                'availability_type'     => $validatedData['availability_type'],
                'availability_quantity' => $validatedData['availability_quantity'],
                'location' => ['label' => $validatedData['availability_location']],
            ]
        ];
    }
}
