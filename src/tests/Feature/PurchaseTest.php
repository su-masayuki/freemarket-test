<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_purchase_item()
    {
        $user = User::factory()->create([
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->get("/purchase/complete/{$item->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_sold_label_is_displayed_on_purchased_item()
    {
        $user = User::factory()->create([
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)->get("/purchase/complete/{$item->id}");

        $response = $this->get('/');

        $response->assertSee('SOLD');
    }

    public function test_purchased_item_appears_in_profile_buy_list()
    {
        $user = User::factory()->create([
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)->get("/purchase/complete/{$item->id}");

        $response = $this->get('/mypage?page=buy');

        $response->assertSee($item->name);
    }


    public function test_selected_payment_method_is_reflected()
    {
        $user = User::factory()->create([
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->put("/purchase/address/{$item->id}", [
            'payment_method' => 'credit_card',
            '_method' => 'PUT', 
        ]);

        $response->assertRedirect('/');

        $response = $this->get("/purchase/{$item->id}");

        $response->assertSee('クレジットカード');
    }

    public function test_shipping_address_is_reflected_in_purchase_screen()
    {
        $user = User::factory()->create([
            'zipcode' => '987-6543',
            'address' => '大阪府大阪市1-2-3',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)->put("/purchase/address/{$item->id}", [
            'zipcode' => '999-8888',
            'address' => '北海道札幌市9-9-9',
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $response->assertSee('999-8888');
        $response->assertSee('北海道札幌市9-9-9');
    }

    public function test_shipping_address_is_saved_with_purchase()
    {
        $user = User::factory()->create([
            'zipcode' => '987-6543',
            'address' => '大阪府大阪市1-2-3',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)->put("/purchase/address/{$item->id}", [
            'zipcode' => '999-8888',
            'address' => '北海道札幌市9-9-9',
        ]);

        $this->get("/purchase/complete/{$item->id}");

        $this->assertDatabaseHas('shipping_addresses', [
            'user_id' => $user->id,
            'zipcode' => '999-8888',
            'address' => '北海道札幌市9-9-9',
        ]);
    }

    // public function test_user_can_complete_purchase_via_purchase_button()
    // {
    //     $user = User::factory()->create([
    //         'zipcode' => '123-4567',
    //         'address' => '東京都新宿区1-2-3',
    //     ]);
    //     $item = Item::factory()->create();

    //     $this->actingAs($user);

    //     $response = $this->post("/purchase/{$item->id}");

    //     $response->assertRedirect(route('purchase.complete', ['item' => $item->id]));
    //     $this->assertDatabaseHas('purchases', [
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //     ]);
    // }

    // public function test_user_can_complete_purchase_with_convenience_store_payment()
    // {
    //     $user = User::factory()->create([
    //         'zipcode' => '123-4567',
    //         'address' => '東京都渋谷区1-2-3',
    //     ]);
    //     $item = Item::factory()->create();

    //     $this->actingAs($user);

    //     // 支払い方法を設定
    //     $this->put("/purchase/address/{$item->id}", [
    //         'zipcode' => '111-1111',
    //         'address' => '東京都渋谷区4-5-6',
    //         'payment_method' => 'convenience_store',
    //     ]);

    //     // 購入処理
    //     $response = $this->post("/purchase/{$item->id}");

    //     $response->assertRedirect(route('purchase.complete', ['item' => $item->id]));
    //     $this->assertDatabaseHas('purchases', [
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //     ]);
    // }
}

    