<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\TransactionRating;

class TransactionController extends Controller
{
    /**
     * 取引を完了する（購入者が押す）
     */
    public function complete(Item $item)
    {
        // 完了後に評価モーダルを表示するフラグをセット
        Session::flash('showRatingModal', true);

        // 取引チャット画面にリダイレクト
        return redirect()->route('transaction_messages.index', $item->id)
            ->with('status', '取引を完了しました。');
    }

    /**
     * 取引相手を評価する
     */
    public function rate(Request $request, Item $item)
    {
        // $request->validate([
        //     'rating' => 'required|integer|min:1|max:5',
        //     'target_user_id' => 'required|exists:users,id',
        // ]);

        TransactionRating::create([
            'item_id' => $item->id,
            'rater_id' => Auth::id(),
            'ratee_id' => $request->target_user_id,
            'rating' => $request->rating,
        ]);

        if ($item->user_id !== Auth::id()) {
            $item->is_sold = true;
            $item->save();
        }

        if ($item->user_id === Auth::id()) {
            Session::flash('showSellerRatingModal', true);
        }

        return redirect()->route('home')
            ->with('success', '評価を送信しました。');
    }
}