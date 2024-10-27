<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class ProfileManagementDisableRequest extends FormRequest
{
    protected $errorBag = 'profileManagementDisableRequest';

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
