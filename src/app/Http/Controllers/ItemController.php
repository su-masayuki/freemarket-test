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

        if ($request->input('page') === 'mylist') {
            if (auth()->check()) {
                $likedItems = auth()->user()->likes()->pluck('item_id');
                $query->whereIn('id', $likedItems);

                // 検索キーワードがある場合は部分一致検索をさらに適用
                if ($request->filled('keyword')) {
                    $query->where('name', 'like', '%' . $request->keyword . '%');
                }
            } else {
                $query->whereRaw('0 = 1'); // 非ログイン時は空にする
            }
        } else {
            // 自分が出品した商品は除外
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }

            // 検索キーワードがある場合は部分一致検索
            if ($request->filled('keyword')) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
            }
        }

        $items = $query->get();

        $keyword = $request->keyword;
        return view('index', compact('items', 'keyword'));
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

        $isLiked = $user->likes()->where('item_id', $item->id)->exists();

        if ($isLiked) {
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
            'brand_name' => 'required|string|max:255',
            'price' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048',
            'condition' => 'required|string',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $imagePath = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'name' => $request->name,
            'brand_name' => $request->brand_name,
            'price' => $request->price,
            'description' => $request->description,
            'image_path' => $imagePath,
            'condition' => $request->condition,
            'user_id' => auth()->id(),
        ]);

        $item->categories()->attach($request->category_ids);

        return redirect()->route('home')->with('success', '商品を出品しました');
    }
}