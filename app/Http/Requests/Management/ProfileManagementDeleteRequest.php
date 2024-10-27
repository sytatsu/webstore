<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class ProfileManagementDeleteRequest extends FormRequest
{
    protected $errorBag = 'profileManagementDeleteRequest';

    public function rules(): array
    {
        return [];
    }
}
