<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Like;

class LikeTest extends TestCase
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

    public function test_user_can_like_an_item()
    {
        // ユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // ユーザーとしてログイン
        $this->actingAs($user);

        // いいね実行前のいいね数を確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいねリクエスト送信
        $response = $this->post(route('items.like', $item->id));

        // リダイレクトなどレスポンス確認
        $response->assertStatus(302);

        // いいねが登録されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいねが登録され、いいね合計値（likes_count）が1になっていることを確認
        $item->refresh();
        $this->assertEquals(1, $item->likes_count);
    }

    public function test_liked_icon_changes_color()
    {
        // ユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // ユーザーとしてログイン
        $this->actingAs($user);

        // いいねを実行
        $this->post(route('items.like', $item->id));

        // 商品詳細ページへアクセス
        $response = $this->get("/item/{$item->id}");

        // いいねされた状態のアイコンが赤色で表示されていることを確認
        $response->assertSee('<img src="http://localhost/images/like_icon_red.png"', false);
    }

    public function test_user_can_unlike_an_item()
    {
        // ユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // ユーザーとしてログイン
        $this->actingAs($user);

        // 最初にいいねする
        $this->post(route('items.like', $item->id));
        $item->refresh();
        $this->assertEquals(1, $item->likes_count);

        // もう一度いいねボタンを押して取り消し（unlike）
        $this->post(route('items.like', $item->id));
        $item->refresh();

        // 2回目の押下でいいね解除（unlike）され、いいね合計値が0になっていることを確認
        // いいねが削除されたことを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が0になっていることを確認
        $this->assertEquals(0, $item->likes_count);
    }
}