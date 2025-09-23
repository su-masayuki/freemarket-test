<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function edit(Item $item)
    {
        $user = auth()->user();
        return view('address_edit', compact('item', 'user'));
    }

    public function update(AddressRequest $request, Item $item)
    {
        $user = auth()->user();
        $user->zipcode = $request->input('zipcode');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()
            ->route('purchase.show', $item)
            ->with('success', '住所を更新しました');
    }
}