<?php

namespace App\Http\Requests\Warehouse\Product;

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
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'brand' => ['required', 'string', 'exists:brands,uuid'],
            'category' => ['required', 'string', 'exists:categories,uuid'],
            'product_type' => ['required', 'string', Rule::enum(ProductTypeEnum::class)],
            'product_variant_type' => ['required', 'string', Rule::enum(ProductVariantType::class)],
        ];
    }
}
