<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FreemarketTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_requires_name()
    {
        $response = $this->post('/register', [
            'name' => '', // intentionally left blank
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    public function test_register_requires_email()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_register_requires_password()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_register_requires_password_minimum_length()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'abc123', // 6文字
            'password_confirmation' => 'abc123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_register_requires_password_confirmation_match()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }


    public function test_register_successfully_redirects_to_email_verify()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_login_requires_email()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_login_requires_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_login_with_invalid_credentials_shows_error_message()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'unregistered@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function test_login_with_valid_credentials_logs_in_successfully()
    {
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }

    public function test_user_can_logout_successfully()
    {
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest(); // 確実にログアウトされたか確認
        $response->assertRedirect('/'); // ホームに戻る想定
    }

public function test_all_items_are_displayed_on_index_page()
{
    $items = \App\Models\Item::factory()->count(5)->create();

    $response = $this->get('/');

    foreach ($items as $item) {
        $response->assertSee($item->name);
    }
}

    public function test_sold_items_are_labeled_as_sold()
    {
        $user = \App\Models\User::factory()->create();

        $item = \App\Models\Item::create([
            'name' => 'Sold商品',
            'price' => 5000,
            'description' => '説明文',
            'image_path' => 'sold.jpg',
            'condition' => '新品',
            'user_id' => $user->id,
            'is_sold' => true,
        ]);

        $response = $this->get('/');

        $response->assertSee('Sold');
    }

    public function test_own_items_are_not_displayed_on_index_page()
    {
        $user = \App\Models\User::factory()->create();

        // 商品（ログインユーザーが出品）
        \App\Models\Item::create([
            'name' => '自分の商品',
            'price' => 3000,
            'description' => 'これは自分の出品です',
            'image_path' => 'self.jpg',
            'condition' => '新品',
            'user_id' => $user->id,
            'is_sold' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertDontSee('自分の商品');
    }

    public function test_only_liked_items_are_displayed_on_mylist_page()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $likedItem = \App\Models\Item::factory()->create();
        $unlikedItem = \App\Models\Item::factory()->create();

        $user->likes()->attach($likedItem->id);

        $response = $this->get('/?page=mylist');

        $response->assertSee($likedItem->name);
        $response->assertDontSee($unlikedItem->name);
    }
}