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
        $items = \App\Models\Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_purchased_items_are_labeled_as_sold()
    {
        $buyer = \App\Models\User::factory()->create();

        $item = \App\Models\Item::factory()->create([
            'name' => '購入済み商品',
            'is_sold' => true,
        ]);

        \App\Models\Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertSee('購入済み商品');
        $response->assertSee('SOLD');
    }


    public function test_items_created_by_authenticated_user_are_not_displayed()
    {

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        \App\Models\Item::factory()->create([
            'name' => '自分の商品',
            'user_id' => $user->id,
        ]);

        \App\Models\Item::factory()->create([
            'name' => '他人の商品',
        ]);

        $response = $this->get('/');


        $response->assertStatus(200);

        $response->assertSee('他人の商品');

        $response->assertDontSee('自分の商品');
    }

    public function test_search_returns_items_matching_keyword()
    {

        \App\Models\Item::factory()->create(['name' => 'レザーバッグ']);
        \App\Models\Item::factory()->create(['name' => 'スポーツシューズ']);

        $response = $this->get('/?keyword=バッグ');

        $response->assertStatus(200);

        $response->assertSee('レザーバッグ');

        $response->assertDontSee('スポーツシューズ');
    }
    public function test_search_keyword_is_retained_in_view()
    {

        $keyword = 'カバン';
        $response = $this->get('/?keyword=' . urlencode($keyword));

        $response->assertStatus(200);

        $response->assertSee($keyword);
    }

    public function test_search_keyword_is_maintained_when_switching_to_mylist()
    {
        $keyword = 'バッグ';

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/?page=mylist&keyword=' . urlencode($keyword));

        $response->assertStatus(200);
        $response->assertSee($keyword);
    }
}