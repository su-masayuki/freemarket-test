<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        // 検索キーワードがある場合は部分一致検索
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        // マイリストページ：セッションからいいねアイテムを取得
        if ($request->input('page') === 'mylist') {
            $likedItems = session('liked_items', []);
            $query->whereIn('id', $likedItems);
        } else {
            // 自分が出品した商品は除外
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }
        }

        $items = $query->get();

        return view('index', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load(['comments.user'])->loadCount('comments');
        $comments = $item->comments;

        return view('item_detail', compact('item', 'comments'));
    }

    public function like(Item $item)
    {
        $user = auth()->user();

        if ($user->likes->contains($item->id)) {
            $user->likes()->detach($item->id);
            $item->decrement('likes_count');
        } else {
            $user->likes()->attach($item->id);
            $item->increment('likes_count');
        }

        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'description' => 'nullable|string',
            'image_path' => 'required|string',
            'condition' => 'required|string',
        ]);

        Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image_path' => $request->image_path,
            'condition' => $request->condition,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('home')->with('success', '商品を出品しました');
    }
}