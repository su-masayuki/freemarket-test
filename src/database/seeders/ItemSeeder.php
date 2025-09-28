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
        $userTaro = User::firstOrCreate(
            ['email' => 'taro@example.com'],
            [
                'name' => 'テスト太郎',
                'password' => bcrypt('password'),
                'zipcode' => '123-4567',
                'address' => '東京都1-1',
            ]
        );

        $userJiro = User::firstOrCreate(
            ['email' => 'jiro@example.com'],
            [
                'name' => 'テスト次郎',
                'password' => bcrypt('password'),
                'zipcode' => '234-5678',
                'address' => '北海道2-2',
            ]
        );

        $userSaburo = User::firstOrCreate(
            ['email' => 'saburo@example.com'],
            [
                'name' => 'テスト三郎',
                'password' => bcrypt('password'),
                'zipcode' => '345-6789',
                'address' => '沖縄県3-3',
            ]
        );

        $category = Category::firstOrCreate(
            ['id' => 1],
            ['name' => 'デフォルトカテゴリ']
        );

        $itemsTaro = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'user_id' => $userTaro->id,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => $userTaro->id,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'user_id' => $userTaro->id,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'user_id' => $userTaro->id,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'user_id' => $userTaro->id,
            ],
        ];

        $itemsJiro = [
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => $userJiro->id,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'user_id' => $userJiro->id,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'user_id' => $userJiro->id,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'user_id' => $userJiro->id,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => $userJiro->id,
            ],
        ];

        $allCategories = Category::all()->pluck('id', 'name');

        foreach (array_merge($itemsTaro, $itemsJiro) as $item) {
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