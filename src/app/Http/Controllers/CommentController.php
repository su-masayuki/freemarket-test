<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'body' => 'required|string|max:255',
        ]);

        $item->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'コメントを投稿しました');
    }
}
