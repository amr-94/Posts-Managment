<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ];
    }

    public function messages(): array
    {
        return [
            //  custom messages
        ];
    }
}