<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class SellItemTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_register_item_listing()
    {
        
        // ダミーユーザー作成・ログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // フォーム送信データ
        $formData = [
            'name' => 'テスト商品',
            'category' => ['id' => 1, 'name' => 'カテゴリ名'],
            'condition' => 'new',
            'description' => 'テスト商品の説明です。',
            'price' => 5000,
        ];

        // POSTリクエストで出品処理
        $response = $this->post('/sell', $formData);

        // リダイレクト先とDBへの保存を確認
        $response->assertRedirect('/');
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'category' => json_encode(['id' => 1, 'name' => 'カテゴリ名']),
            'condition' => 'new',
            'description' => 'テスト商品の説明です。',
            'price' => 5000,
        ]);
    }
}
