<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_profile_page_displays_user_info_and_items()
    {
        $user = \App\Models\User::factory()
            ->has(\App\Models\Item::factory()->count(2), 'items')
            ->create([
                'name' => 'テストユーザー',
            ]);

        // 購入商品を1件作成
        $purchasedItem = \App\Models\Item::factory()->create();
        \App\Models\Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('プロフィールを編集');
        $response->assertSee('出品した商品');
        $response->assertSee('購入した商品');
    }
    public function test_profile_edit_page_displays_existing_user_info()
    {
        $user = \App\Models\User::factory()->create([
            'name' => '太郎',
            'zipcode' => '123-4567',
            'address' => '東京都港区1-1-1',
            'image_path' => 'profile.jpg',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('太郎');
        $response->assertSee('123-4567');
        $response->assertSee('東京都港区1-1-1');
        $response->assertSee('profile.jpg');
    }
}
