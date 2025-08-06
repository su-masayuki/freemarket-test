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

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);


        $category = \App\Models\Category::factory()->create();


        $formData = [
            'name' => 'テスト商品',
            'category' => [$category->id],
            'condition' => 'new',
            'description' => 'テスト商品の説明です。',
            'price' => 5000,
            'brand_name' => 'テストブランド',
        ];

        $response = $this->post('/sell', $formData);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'condition' => 'new',
            'description' => 'テスト商品の説明です。',
            'price' => 5000,
            'brand_name' => 'テストブランド',
        ]);

        $item = \App\Models\Item::where('name', 'テスト商品')->first();
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);
    }
}
