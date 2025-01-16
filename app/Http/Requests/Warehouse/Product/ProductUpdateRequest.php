<?php

namespace App\Http\Requests\Warehouse\Product;

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use App\Services\Warehouse\ProductService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $rules = [
            'uuid'                 => ['required', 'uuid', 'exists:products,uuid'],
            'name'                 => ['required', 'string', 'max:100'],
            'description'          => ['required', 'string'],
            'brand'                => ['required', 'string', 'exists:brands,uuid'],
            'category'             => ['required', 'string', 'exists:categories,uuid'],
            'product_type'         => ['required', 'string', Rule::enum(ProductTypeEnum::class)],
            'product_variant_type' => ['required', 'string', Rule::enum(ProductVariantType::class)],
        ];

        if(app(ProductService::class)->getProduct($this->get('uuid'))->productVariantType->value === ProductVariantType::UNIQUE->value) {
            $rules = array_merge($rules, [
                'product_variant_price'      => ['required', 'string'],
                'product_variant_sku'        => ['required', 'string'],
                'product_variant_variants'   => ['required', 'array'],
                'product_variant_variants.*' => ['string', 'exists:variants,uuid'],
            ]);
        }

        return $rules;
    }

    public function validated($key = null, $default = null): array
    {
        $validatedData = $this->except('_token', '_method');

        if ($key !== null) {
            return $validatedData;
        }

        $formatted['product'] = [
            'name'                 => $validatedData['name'],
            'description'          => $validatedData['description'],
            'brand'                => $validatedData['brand'],
            'category'             => $validatedData['category'],
            'product_type'         => $validatedData['product_type'],
            'product_variant_type' => $validatedData['product_variant_type'],
        ];

        if ( app(ProductService::class)->getProduct($this->get('uuid'))->productVariantType->value === ProductVariantType::UNIQUE->value) {
            $formatted['product_variant'] = [
                'price'    => $validatedData['product_variant_price'],
                'sku'      => $validatedData['product_variant_sku'],
                'variants' => $validatedData['product_variant_variants'],
            ];
        }

        return $formatted;

    }
}
