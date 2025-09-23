<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'zipcode' => [
                'required',
                'size:8',
                'regex:/^\d{3}-\d{4}$/'
            ],
            'address' => 'required',
            'building' => 'nullable'
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'お名前を入力して下さい',
            'zipcode.required' => '郵便番号を入力して下さい',
            'zipcode.size' => '郵便番号は8文字で入力してください（例: 123-4567）',
            'zipcode.regex' => '郵便番号の形式が正しくありません（例: 123-4567）',
            'address.required' => '住所を入力して下さい',
        ];
    }
}
