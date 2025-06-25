<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('page') === 'mylist') {
            $likedItems = session('liked_items', []);
            $items = Item::whereIn('id', $likedItems)->get();
        } else {
            $items = Item::all();
        }

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
        $likedItems = session()->get('liked_items', []);

        if (in_array($item->id, $likedItems)) {
            $item->decrement('likes_count');
            $likedItems = array_diff($likedItems, [$item->id]);
        } else {
            $item->increment('likes_count');
            $likedItems[] = $item->id;
        }

        session(['liked_items' => $likedItems]);

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