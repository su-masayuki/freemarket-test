<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MylistPageTest extends TestCase
{
    use RefreshDatabase;
    public function test_only_liked_items_are_displayed_in_mylist()
    {

        // ユーザー作成・ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 商品を複数作成
        $likedItem = \App\Models\Item::factory()->create();
        $unlikedItem = \App\Models\Item::factory()->create();

        // いいねを1件だけ登録
        $user->likes()->attach($likedItem->id);

        // マイリストページにアクセス
        $response = $this->get('/?page=mylist');

        // 表示されている商品を確認
        $response->assertStatus(200);
        $response->assertSee($likedItem->name);
        $response->assertDontSee($unlikedItem->name);
    }

    public function test_purchased_items_are_labeled_as_sold_in_mylist()
    {
        // ユーザー作成・ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 購入済み商品を作成（is_sold = true）
        $purchasedItem = \App\Models\Item::factory()->create([
            'is_sold' => true,
        ]);

        // ユーザーが購入済み商品をいいね（マイリストに表示させるため）
        $user->likes()->attach($purchasedItem->id);

        // マイリストページにアクセス
        $response = $this->get('/?page=mylist');

        // SOLDラベルが表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    public function test_own_items_are_not_displayed_in_mylist()
    {
        // ユーザー作成・ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 自分の商品を作成
        $ownItem = \App\Models\Item::factory()->create([
            'user_id' => $user->id,
        ]);

        // 他人の商品を作成していいね
        $otherItem = \App\Models\Item::factory()->create();
        $user->likes()->attach($otherItem->id);

        // マイリストページにアクセス
        $response = $this->get('/?page=mylist');

        // 自分の商品は表示されず、他人の商品は表示されることを確認
        $response->assertStatus(200);
        $response->assertSee($otherItem->name);
        $response->assertDontSee($ownItem->name);
    }
    public function test_mylist_is_empty_when_not_authenticated()
    {
        // 未ログイン状態でマイリストページにアクセス
        $response = $this->get('/?page=mylist');

        // ステータスコードは200で、商品名などが表示されていないことを確認
        $response->assertStatus(200);
        $response->assertDontSee('item-card'); // 商品カードクラスなど表示内容に応じて調整
    }
}