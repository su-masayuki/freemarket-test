<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition():array
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'body' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
