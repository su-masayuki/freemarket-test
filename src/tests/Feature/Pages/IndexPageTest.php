<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexPageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_all_items_are_displayed()
    {
        // 商品を3件作成（必要に応じてファクトリを使って数を調整）
        $items = \App\Models\Item::factory()->count(3)->create();

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスが200か確認
        $response->assertStatus(200);

        // 各商品の名前がレスポンスに含まれていることを確認
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_purchased_items_are_labeled_as_sold()
    {
        // 購入者を作成
        $buyer = \App\Models\User::factory()->create();

        // 購入済み商品を作成
        $item = \App\Models\Item::factory()->create([
            'name' => '購入済み商品',
            'is_sold' => true,
        ]);

        // purchasesテーブルにレコードを作成（購入情報）
        \App\Models\Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスが200か確認
        $response->assertStatus(200);

        // 商品名とSOLDラベルが表示されていることを確認
        $response->assertSee('購入済み商品');
        $response->assertSee('SOLD');
    }

    // public function test_items_created_by_authenticated_user_are_not_displayed()
    // {
    //     // 出品者を作成してログイン
    //     $user = \App\Models\User::factory()->create();
    //     $this->actingAs($user);

    //     // 出品者自身の商品を作成
    //     \App\Models\Item::factory()->create([
    //         'name' => '自分の商品',
    //         'user_id' => $user->id,
    //     ]);

    //     // 他のユーザーの商品を作成
    //     \App\Models\Item::factory()->create([
    //         'name' => '他人の商品',
    //     ]);

    //     // 商品一覧ページにアクセス
    //     $response = $this->get('/');

    //     // ステータスが200か確認
    //     $response->assertStatus(200);

    //     // 他人の商品は表示されていることを確認
    //     $response->assertSee('他人の商品');

    //     // 自分の商品は表示されていないことを確認
    //     $response->assertDontSee('自分の商品');
    // }

    public function test_search_returns_items_matching_keyword()
    {
        // 商品を作成（検索にヒットするものとしないもの）
        \App\Models\Item::factory()->create(['name' => 'レザーバッグ']);
        \App\Models\Item::factory()->create(['name' => 'スポーツシューズ']);

        // 検索ワード「バッグ」で検索
        $response = $this->get('/?keyword=バッグ');

        // ステータスが200か確認
        $response->assertStatus(200);

        // 検索結果に「レザーバッグ」が含まれていること
        $response->assertSee('レザーバッグ');

        // 検索結果に「スポーツシューズ」が含まれていないこと
        $response->assertDontSee('スポーツシューズ');
    }
    public function test_search_keyword_is_retained_in_view()
    {
        // 検索ワードを指定してリクエストを送信
        $keyword = 'カバン';
        $response = $this->get('/?keyword=' . urlencode($keyword));

        // ステータスが200であることを確認
        $response->assertStatus(200);

        // レスポンスに検索キーワードが保持されて表示されていることを確認
        $response->assertSee($keyword);
    }

    public function test_search_keyword_is_maintained_when_switching_to_mylist()
    {
        $keyword = 'バッグ';

        // ログインユーザーを作成してログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // マイリストページに検索付きでアクセス
        $response = $this->get('/?page=mylist&keyword=' . urlencode($keyword));

        $response->assertStatus(200);
        $response->assertSee($keyword);
    }
}