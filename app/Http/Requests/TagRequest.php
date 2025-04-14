<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        $tagId = $this->route('id'); // للتحديث
        return [
            'name' => 'required|string|max:255|unique:tags,name,' . $tagId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الوسم مطلوب',
            'name.string' => 'اسم الوسم يجب أن يكون نصاً',
            'name.max' => 'اسم الوسم يجب ألا يتجاوز 255 حرفاً',
            'name.unique' => 'هذا الوسم موجود بالفعل',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
