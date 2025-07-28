<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'taro' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::firstOrCreate(
            ['id' => 1],
            ['name' => 'デフォルトカテゴリ']
        );

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'user_id' => $user->id,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => $user->id,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'user_id' => $user->id,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'user_id' => $user->id,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'user_id' => $user->id,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => $user->id,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'user_id' => $user->id,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'user_id' => $user->id,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'user_id' => $user->id,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => $user->id,
            ],
        ];

        // すべてのカテゴリを取得
        $allCategories = Category::all()->pluck('id', 'name');

        foreach ($items as $item) {
            $createdItem = Item::create($item);

            $name = $item['name'];
            $categoryIds = [];

            if (str_contains($name, '腕時計')) {
                $categoryIds[] = $allCategories['ファッション'] ?? $category->id;
            } elseif (str_contains($name, 'HDD') || str_contains($name, 'ノートPC') || str_contains($name, 'マイク')) {
                $categoryIds[] = $allCategories['家電'] ?? $category->id;
            } elseif (str_contains($name, '玉ねぎ')) {
                $categoryIds[] = $allCategories['キッチン'] ?? $category->id;
            } elseif (str_contains($name, '革靴') || str_contains($name, 'ショルダーバッグ')) {
                $categoryIds[] = $allCategories['ファッション'] ?? $category->id;
            } elseif (str_contains($name, 'タンブラー') || str_contains($name, 'コーヒーミル')) {
                $categoryIds[] = $allCategories['キッチン'] ?? $category->id;
            } elseif (str_contains($name, 'メイクセット')) {
                $categoryIds[] = $allCategories['コスメ'] ?? $category->id;
            }

            if (!empty($categoryIds)) {
                $createdItem->categories()->attach($categoryIds);
            } else {
                $createdItem->categories()->attach($category->id);
            }
        }
    }
}