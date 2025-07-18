<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(1000, 10000),
            'description' => $this->faker->sentence(),
            'image_path' => 'dummy.jpg',
            'condition' => '新品',
            'user_id' => \App\Models\User::factory(),
            'is_sold' => false,
        ];
    }
}
