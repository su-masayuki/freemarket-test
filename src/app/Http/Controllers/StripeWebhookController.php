<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid webhook'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;

            $itemId = $intent->metadata->item_id ?? null;
            $userId = $intent->metadata->user_id ?? null;

            if ($itemId && $userId) {
                $item = Item::find($itemId);
                $user = User::find($userId);

                if ($item && $user && !$item->is_sold) {
                    $item->is_sold = true;
                    $item->save();

                    Purchase::create([
                        'user_id' => $user->id,
                        'item_id' => $item->id,
                        'shipping_address_id' => $user->shippingAddresses()->latest()->first()->id, // 要調整
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}