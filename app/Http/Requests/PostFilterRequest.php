<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'keyword' => 'nullable|string|min:3',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }
}
