<?php

// app/Http/Requests/PurchaseRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payment_method' => 'required',
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'shipping_address_id.required' => '配送先を選択してください。',
            'shipping_address_id.exists' => '選択された配送先が存在しません。',
        ];
    }
}