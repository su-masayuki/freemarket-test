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

        // 購入処理
        $this->actingAs($user)->get("/purchase/complete/{$item->id}");

        // 商品一覧ページを確認
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

        // 支払い方法をセッションに保存するリクエストをシミュレート
        $response = $this->put("/purchase/address/{$item->id}", [
            'payment_method' => 'credit_card',
            '_method' => 'PUT', // HTMLフォームからのPUTメソッドエミュレーション
        ]);

        $response->assertRedirect('/');

        // 購入確認画面で選択が反映されているか確認
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
}

    