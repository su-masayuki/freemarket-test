<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionMessage;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TransactionMessageRequest;

class TransactionMessageController extends Controller
{
    public function index($itemId)
    {
        $item = Item::with('user')->findOrFail($itemId);
        $messages = $item->messages()->with('sender')->latest()->get();
        $isSeller = $item->user_id === Auth::id();

        $user = Auth::user();
        $tradingItems = Item::where('is_sold', false)
            ->whereHas('purchases') // 購入者が存在する商品のみ
            ->where(function ($query) use ($user) {
                // 購入者として関わっている商品
                $query->whereHas('purchases', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->orWhere(function ($query) use ($user) {
                // 出品者として関わっている商品（必ず購入者が存在する）
                $query->where('user_id', $user->id)
                      ->whereHas('purchases');
            })
            ->get();

        // 既読処理を追加
        foreach ($messages as $message) {
            DB::table('transaction_messages_reads')->updateOrInsert(
                ['message_id' => $message->id, 'user_id' => Auth::id()],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return view('transaction.index', compact('item', 'messages', 'isSeller', 'tradingItems'));
    }

    public function store(TransactionMessageRequest $request, $itemId)
    {
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

    public function destroy($itemId, $messageId)
    {
        $message = TransactionMessage::findOrFail($messageId);

        // 本人以外は削除できない
        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        // 対象メッセージが指定の商品に紐づいているか確認
        if ($message->item_id != $itemId) {
            abort(404);
        }

        // 最後に送ったメッセージのみ削除可能
        $latestMessage = TransactionMessage::where('item_id', $itemId)
            ->where('sender_id', Auth::id())
            ->latest()
            ->first();

        if (!$latestMessage || $latestMessage->id !== $message->id) {
            return back()->with('error', '最後に送ったメッセージのみ削除可能です。');
        }

        // 画像があれば削除
        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }

    public function edit($itemId, $messageId)
    {
        $message = TransactionMessage::findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        return view('transaction.edit', compact('message', 'itemId'));
    }

    public function update(TransactionMessageRequest $request, $itemId, $messageId)
    {
        $message = TransactionMessage::findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        $message->message = $request->input('message');
        $message->save();

        return redirect()->route('transaction_messages.index', $itemId)
            ->with('success', 'メッセージを更新しました。');
    }
}
