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
        if ($item->user_id !== Auth::id()) {
        
            return redirect()->route('transaction_messages.index', ['item' => $item->id])
                ->with('showRatingModal', true)
                ->with('status', '取引を完了しました。');
        } else {
       
            return redirect()->route('transaction_messages.index', ['item' => $item->id])
                ->with('showSellerRatingModal', true)
                ->with('status', '取引を完了しました。');
        }
    }

    public function rate(Request $request, Item $item)
    {

        TransactionRating::create([
            'item_id' => $item->id,
            'rater_id' => Auth::id(),
            'ratee_id' => $request->target_user_id,
            'rating' => $request->rating,
            'buyer_completed' => ($item->user_id !== Auth::id()),  
            'seller_completed' => ($item->user_id === Auth::id()),  
        ]);

        $buyerDone  = TransactionRating::where('item_id', $item->id)->where('buyer_completed', 1)->exists();
        $sellerDone = TransactionRating::where('item_id', $item->id)->where('seller_completed', 1)->exists();

        if ($buyerDone && $sellerDone) {
            $item->is_sold = true;
            $item->save();
        }

        if ($item->user_id !== Auth::id()) {
            Mail::raw(
                "商品「{$item->name}」の取引が完了しました。",
                function ($message) use ($item) {
                    $message->to($item->user->email)
                            ->subject('【取引完了通知】' . $item->name);
                }
            );
        }

        return redirect()->route('home')
            ->with('success', '評価を送信しました。');
    }
}