<?php

namespace App\Http\Requests\Warehouse\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryDeleteRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'exists:categories,uuid'],
        ];
    }
}
