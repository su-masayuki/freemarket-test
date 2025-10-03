<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\TransactionRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $averageRating = round(TransactionRating::where('ratee_id', $user->id)->avg('rating'));
        $page = request('page');

        if ($page === 'buy') {
            $items = Item::where('is_sold', true)
                ->whereHas('purchases', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->get(); 
        } elseif ($page === 'trading') {
            $items = Item::where('is_sold', false)
                ->whereHas('purchases') 
                ->where(function ($query) use ($user) {
                    $query->whereHas('purchases', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->orWhere('user_id', $user->id);
                })
                ->get();
        } else {
            $items = $user->items ?? collect(); 
        }

        $tradingItems = Item::where('is_sold', false)
            ->whereHas('purchases')
            ->where(function ($query) use ($user) {
                $query->whereHas('purchases', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orWhere('user_id', $user->id);
            })
            ->get();

        return view('mypage', [
            'items' => $items,
            'page' => $page,
            'tradingItems' => $tradingItems,
            'averageRating' => $averageRating,
        ]);
    }

    public function edit()
    {
        $user = Auth::user();
        return view('edit_profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'zipcode' => 'required|string|max:8',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $user->name = $request->input('name');
        $user->zipcode = $request->input('zipcode');
        $user->address = $request->input('address');
        $user->building = $request->input('building');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/profiles', 'public');
            $user->image_path = $path;
        }

        $user->save();

        if ($request->input('from') === 'first') {
            return redirect('/')->with('success', 'プロフィールを更新しました');
        }

        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました');
    }
}