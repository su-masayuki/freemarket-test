<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::user(); // 現在ログイン中のユーザー
        return view('purchase', compact('item', 'user'));
    }

    public function store(Request $request, Item $item)
    {
        // 購入済みに更新
        $item->is_sold = true;
        $item->save();

        $item->purchasers()->attach(Auth::id());

        // 商品一覧画面にリダイレクト
        return redirect()->route('home')->with('success', '購入が完了しました');
    }
}
