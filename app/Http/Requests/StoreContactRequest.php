<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'gender'      => ['required', 'in:1,2,3'],
            'email'       => ['required', 'email', 'max:255'],
            'tel'         => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'detail'      => ['required', 'string', 'max:120'],
            'tag_ids'     => ['nullable', 'array'],
            'tag_ids.*'   => ['exists:tags,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // tel1, tel2, tel3 がバラバラで送られてきた場合に結合する処理
        if ($this->has('tel1') && $this->has('tel2') && $this->has('tel3')) {
            $this->merge([
                'tel' => $this->tel1 . $this->tel2 . $this->tel3,
            ]);
        }
    }
}