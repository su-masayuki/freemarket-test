<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_submit_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comments", [
            'body' => 'テストコメント',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'テストコメント',
        ]);

        $item->refresh();
        $this->assertEquals(1, $item->comments()->count());
    }

    public function test_guest_cannot_submit_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comments", [
            'body' => 'ゲストコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'body' => 'ゲストコメント',
        ]);
    }

    public function test_comment_body_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comments", [
            'body' => '',
        ]);

        $response->assertSessionHasErrors(['body']);
    }

    public function test_comment_body_must_be_under_255_characters()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comments", [
            'body' => str_repeat('あ', 256),
        ]);

        $response->assertSessionHasErrors(['body']);
    }
}
