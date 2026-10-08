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
        // 確認画面から「修正」ボタンで戻る場合はバリデーションを実行しない
        if ($this->has('back')) {
            return [];
        }

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:1,2,3'],
            'email' => ['required', 'email', 'max:255'],
            'tel' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'detail' => ['required', 'string', 'max:120'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
        ];
    }

    /**
     * 要件(a〜j)の文言を一字一句完全に一致させたエラーメッセージ定義
     */
    public function messages(): array
    {
        return [
            'last_name.required' => '姓を入力してください',
            'first_name.required' => '名を入力してください',
            'gender.required' => '性別を選択してください',
            'gender.in' => '性別を選択してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'tel.required' => '電話番号を入力してください',
            'tel.regex' => '電話番号はハイフンなしの10〜11桁で入力してください',
            'address.required' => '住所を入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.exists' => 'お問い合わせの種類を選択してください',
            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問い合わせ内容は120文字以内で入力してください',
        ];
    }

    protected function prepareForValidation(): void
    {
        // tel1, tel2, tel3 がバラバラで送られてきた場合に結合する処理
        if ($this->has('tel1') && $this->has('tel2') && $this->has('tel3')) {
            $this->merge([
                'tel' => $this->tel1.$this->tel2.$this->tel3,
            ]);
        }
    }
}
