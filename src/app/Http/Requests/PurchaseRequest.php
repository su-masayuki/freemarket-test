<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payment_method' => 'required',
            'shipping_address_id' => 'required_without:zipcode|exists:shipping_addresses,id',
            'zipcode' => 'required_without:shipping_address_id',
            'address' => 'required_without:shipping_address_id',
            'building' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'shipping_address_id.required_without' => '配送先を指定してください。',
            'shipping_address_id.exists' => '選択された配送先が存在しません。',
            'zipcode.required_without' => '郵便番号を入力してください。',
            'address.required_without' => '住所を入力してください。',
        ];
    }
}