<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\TransactionRating;

use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function complete(Item $item)
    {
        Session::flash('showRatingModal', true);

        return redirect()->route('transaction_messages.index', $item->id)
            ->with('status', '取引を完了しました。');
    }

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
            Mail::raw(
                "商品「{$item->name}」の取引が完了しました。",
                function ($message) use ($item) {
                    $message->to($item->user->email)
                            ->subject('【取引完了通知】' . $item->name);
                }
            );
        }

        if ($item->user_id === Auth::id()) {
            Session::flash('showSellerRatingModal', true);
        }

        return redirect()->route('home')
            ->with('success', '評価を送信しました。');
    }
}