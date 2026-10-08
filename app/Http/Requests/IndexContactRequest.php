<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // true に変更して認可を許可します
    }

    public function rules(): array
    {
        return [
            'keyword'     => ['nullable', 'string', 'max:255'],
            'gender'      => ['nullable', 'integer', 'in:0,1,2,3'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'date'        => ['nullable', 'date'],
        ];
    }
}
