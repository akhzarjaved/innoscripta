<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ArticleSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source' => ['nullable', 'exists:sources,id'],
            'category' => ['nullable', 'exists:categories,id'],
            'author' => ['nullable', 'exists:authors,id'],
            'keyword' => ['nullable', 'max:50'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
