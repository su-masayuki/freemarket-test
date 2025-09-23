<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionMessage;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class TransactionMessageController extends Controller
{
    public function index($itemId)
    {
        $item = Item::findOrFail($itemId);
        $messages = $item->messages()->with('sender')->latest()->get();

        return view('transaction_messages.index', compact('item', 'messages'));
    }

    public function store(Request $request, $itemId)
    {
        $request->validate([
            'message' => 'nullable|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $item = Item::findOrFail($itemId);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/messages', 'public');
        }

        TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => Auth::id(),
            'message' => $request->input('message'),
            'image_path' => $path,
        ]);

        return redirect()->route('transaction_messages.index', $item->id)
            ->with('success', 'メッセージを送信しました。');
    }

    public function destroy($id)
    {
        $message = TransactionMessage::findOrFail($id);

        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }
}
