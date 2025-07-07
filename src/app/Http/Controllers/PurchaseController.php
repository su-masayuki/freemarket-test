<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\ShippingAddress;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::user(); // 現在ログイン中のユーザー
        return view('purchase', compact('item', 'user'));
    }

    public function store(Request $request, Item $item)
    {
        // 商品を購入済みに設定
        $item->is_sold = true;
        $item->save();

        // 新しい配送先住所を保存
        $shipping = ShippingAddress::create([
            'user_id' => Auth::id(),
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        // 購入記録を保存
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'shipping_address_id' => $shipping->id,
        ]);

        // 商品一覧画面にリダイレクト
        return redirect()->route('home')->with('success', '購入が完了しました');
    }
}
