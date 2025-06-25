<?php

namespace App\Http\Controllers;
use App\Models\Item;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function edit(Item $item)
    {
        $user = auth()->user();
        return view('address_edit', compact('item', 'user'));
    }


    public function update(Request $request, Item $item)
    {
        $request->validate([
            'zipcode' => 'required|string|max:8',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $user->zipcode = $request->input('zipcode');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('purchase.show', $item)->with('success', '住所を更新しました');
    }
}