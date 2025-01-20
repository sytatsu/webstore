<?php

namespace App\Http\Requests\Catalog\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'exists:brands,uuid'],
            'name' => ['required', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
        ];
    }
}
