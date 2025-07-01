<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Item;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    public function create()
    {
        $categories = [
            'ファッション', 'おもちゃ', '家電', 'スポーツ', 'ベビー・キッズ',
            'インテリア', 'レディース', 'メンズ', 'コスメ', '本',
            'ゲーム', 'キッチン', 'ハンドメイド', 'アクセサリー'
        ];
        return view('sell', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'category' => 'required|array',
            'condition' => 'required|string',
            'name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:1',
        ]);

        $path = $request->file('image')->store('images/items', 'public');

        Item::create([
            'name' => $request->name,
            'brand_name' => $request->brand_name,
            'description' => $request->description,
            'price' => $request->price,
            'image_path' => $path,
            'condition' => $request->condition,
            'user_id' => Auth::id(),
            'category' => json_encode($request->category),
        ]);

        return redirect()->route('home')->with('success', '商品を出品しました');
    }
}
