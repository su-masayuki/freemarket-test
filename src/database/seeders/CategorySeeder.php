<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション', 'おもちゃ', '家電', 'スポーツ', 'ベビー・キッズ',
            'インテリア', 'レディース', 'メンズ', 'コスメ', '本',
            'ゲーム', 'キッチン', 'ハンドメイド', 'アクセサリー'
        ];

        foreach ($categories as $index => $name) {
            Category::firstOrCreate(
                ['id' => $index + 1],
                ['name' => $name]
            );
        }
    }
}