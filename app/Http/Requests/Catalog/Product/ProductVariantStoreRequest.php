<?php

namespace App\Http\Requests\Catalog\Product;

use App\Enums\ChannelEnum;
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

            'channel_type'     => ['required', 'string', Rule::enum(ChannelEnum::class)],
            'channel_location' => ['required', 'string', 'exists:channel_locations,label'],
            'channel_quantity' => ['required', 'numeric', 'min:0'],
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

            'channel' => [
                'channel_type'     => $validatedData['channel_type'],
                'channel_quantity' => $validatedData['channel_quantity'],
                'location' => ['label' => $validatedData['channel_location']],
            ]
        ];
    }
}
