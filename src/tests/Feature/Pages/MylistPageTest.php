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

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $likedItem = \App\Models\Item::factory()->create();
        $unlikedItem = \App\Models\Item::factory()->create();

        $user->likes()->attach($likedItem->id);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee($likedItem->name);
        $response->assertDontSee($unlikedItem->name);
    }

    public function test_purchased_items_are_labeled_as_sold_in_mylist()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $purchasedItem = \App\Models\Item::factory()->create([
            'is_sold' => true,
        ]);

        $user->likes()->attach($purchasedItem->id);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    public function test_own_items_are_not_displayed_in_mylist()
    {

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $ownItem = \App\Models\Item::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherItem = \App\Models\Item::factory()->create();
        $user->likes()->attach($otherItem->id);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee($otherItem->name);
        $response->assertDontSee($ownItem->name);
    }
    public function test_mylist_is_empty_when_not_authenticated()
    {

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('item-card'); 
    }
}