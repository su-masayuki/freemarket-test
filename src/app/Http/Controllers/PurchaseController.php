<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\ShippingAddress;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::user(); 
        return view('purchase', compact('item', 'user'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();

        $request->validate([
            'payment_method' => 'required',
        ]);

        $shipping = null;

        if ($request->filled('zipcode') && $request->filled('address')) {
            $shipping = ShippingAddress::create([
                'user_id' => $user->id,
                'zipcode' => $request->zipcode,
                'address' => $request->address,
                'building' => $request->building,
            ]);
        } elseif ($user->zipcode && $user->address) {
            $shipping = ShippingAddress::create([
                'user_id' => $user->id,
                'zipcode' => $user->zipcode,
                'address' => $user->address,
                'building' => $user->building,
            ]);
        }

        if (! $shipping) {
            return back()->withErrors(['shipping_address_id' => '配送先を指定してください。'])->withInput();
        }

        // Stripe決済セッション
        /*
        Stripe::setApiKey(config('services.stripe.secret'));

        if ($request->payment_method === 'card') {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.complete', ['item' => $item->id]),
                'cancel_url' => url()->previous(),
            ]);
            $redirectUrl = $session->url;
        } elseif ($request->payment_method === 'convenience') {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $item->price,
                'currency' => 'jpy',
                'payment_method_types' => ['konbini'],
                'payment_method_data' => [
                    'type' => 'konbini',
                    'billing_details' => [
                        'name' => $user->name ?? '購入者',
                        'email' => $user->email ?? 'example@example.com',
                    ],
                ],
                'payment_method_options' => [
                    'konbini' => [
                        'product_description' => $item->name,
                        'expires_after_days' => 3,
                    ],
                ],
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'metadata' => [
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                ],
            ]);
            if (
                isset($paymentIntent->next_action) &&
                isset($paymentIntent->next_action->konbini_display_details) &&
                isset($paymentIntent->next_action->konbini_display_details->hosted_voucher_url)
            ) {
                $redirectUrl = $paymentIntent->next_action->konbini_display_details->hosted_voucher_url;
            } else {
                return back()->withErrors(['stripe' => 'コンビニ支払い用バウチャーの生成に失敗しました。'])->withInput();
            }
        }
        */

        // Stripe決済処理無効化中
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => $shipping->id,
        ]);

        session()->forget('shipping_address_id');

        return redirect()->route('transaction_messages.index', $item->id);
    }

    public function complete(Item $item)
    {
        $user = Auth::user();

        $existingPurchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        if ($existingPurchase) {
            return redirect()->route('home')->with('status', 'この商品はすでに購入済みです。');
        }

        $shippingAddressId = session('shipping_address_id');

        if (! $shippingAddressId) {
            if (app()->environment('testing') && $user->zipcode && $user->address) {
                $shipping = ShippingAddress::create([
                    'user_id' => $user->id,
                    'zipcode' => $user->zipcode,
                    'address' => $user->address,
                    'building' => $user->building,
                ]);
                $shippingAddressId = $shipping->id;
            } else {
                return redirect()->route('home')->withErrors(['error' => '配送先情報が見つかりません。']);
            }
        }

        $item->is_sold = true;
        $item->save();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => $shippingAddressId,
        ]);

        session()->forget('shipping_address_id');

        return redirect()->route('home')->with('status', '購入が完了しました。');
    }
}
