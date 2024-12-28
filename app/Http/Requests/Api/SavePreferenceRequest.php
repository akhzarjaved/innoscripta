<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SavePreferenceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'preferences' => ['required', 'array'],
            'preferences.*.type' => ['required', 'in:category,source,author'],
            'preferences.*.value' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
