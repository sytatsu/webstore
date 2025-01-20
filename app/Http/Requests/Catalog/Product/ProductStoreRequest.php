<?php

namespace App\Http\Requests\Catalog\Product;

use App\Enums\ChannelEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string', 'max:100'],
            'description'          => ['required', 'string'],
            'brand'                => ['required', 'string', 'exists:brands,uuid'],
            'category'             => ['required', 'string', 'exists:categories,uuid'],
            'product_type'         => ['required', 'string', Rule::enum(ProductTypeEnum::class)],
            'product_variant_type' => ['required', 'string', Rule::enum(ProductVariantType::class)],

            'product_variant_name'        => ['nullable', 'string', 'max:100'],
            'product_variant_description' => ['nullable', 'string'],
            'product_variant_price'       => ['required', 'string'],
            'product_variant_sku'         => ['required', 'string'],
            'product_variant_variants'    => ['required', 'array'],
            'product_variant_variants.*'  => ['string', 'exists:variants,uuid'],

            'product_variant_channel_type'     => ['required', 'string', Rule::enum(ChannelEnum::class)],
            'product_variant_channel_location' => ['required', 'string', 'exists:channel_locations,label'],
            'product_variant_channel_quantity' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validatedData = parent::validated($key, $default);

        if ($key !== null) {
            return $validatedData;
        }

        return [
            'product' => [
                'name'                 => $validatedData['name'],
                'description'          => $validatedData['description'],
                'brand'                => $validatedData['brand'],
                'category'             => $validatedData['category'],
                'product_type'         => $validatedData['product_type'],
                'product_variant_type' => $validatedData['product_variant_type'],
            ],

            'product_variant' => [
                'name'        => $validatedData['product_variant_name'] ?? null,
                'description' => $validatedData['product_variant_description'] ?? null,
                'price'       => $validatedData['product_variant_price'],
                'sku'         => $validatedData['product_variant_sku'],
                'variants'    => $validatedData['product_variant_variants'],

                'channel' => [
                    'channel_type'     => $validatedData['product_variant_channel_type'],
                    'channel_quantity' => $validatedData['product_variant_channel_quantity'],
                    'location' => ['label' => $validatedData['product_variant_channel_location']],
                ]
            ],
        ];
    }
}
