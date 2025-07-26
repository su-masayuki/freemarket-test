<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDetailPageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_item_detail_displays_all_required_information()
    {
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand_name' => 'ブランド名',
            'price' => 12345,
            'description' => '商品説明',
            'category' => json_encode(['カテゴリ1', 'カテゴリ2']),
            'condition' => '新品',
        ]);

        // コメントを追加
        \App\Models\Comment::factory()->count(2)->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        // いいね追加（likes_count を反映）
        $item->update(['likes_count' => 5]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('ブランド名');
        $response->assertSee('¥12,345');
        $response->assertSee('商品説明');
        $response->assertSee('カテゴリ1');
        $response->assertSee('カテゴリ2');
        $response->assertSeeInOrder(['カテゴリ1', 'カテゴリ2']);
        $response->assertSee('新品');
        $response->assertSee('5'); // likes_count
        $response->assertSeeText('コメント');
    }
}
