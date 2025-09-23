<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $page = request('page');

        if ($page === 'buy') {
            $items = $user->purchases ?? collect(); // 購入商品
        } elseif ($page === 'trading') {
            $items = Item::where('is_sold', false)
                ->whereHas('purchases', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->get(); // 取引中の商品
        } else {
            $items = $user->items ?? collect(); // 出品商品
        }

        $tradingItems = Item::where('is_sold', false)
            ->whereHas('purchases', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        return view('mypage', [
            'items' => $items,
            'page' => $page,
            'tradingItems' => $tradingItems,
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