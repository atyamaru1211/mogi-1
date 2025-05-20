<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use Faker\Factory as Faker;


class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('ja_JP');

        $testUser = User::factory()->create();

        $itemData = [
            [
                'id' => 1,
                'user_id' => User::factory()->create()->id,
                'name' => '腕時計',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'items/watch.jpeg',
                'condition' => '1',
            ],
            [
                'id' => 2,
                'user_id' => User::factory()->create()->id,
                'name' => 'HDD',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'items/HDD.jpeg',
                'condition' => '2',
            ],
            [
                'id' => 3,
                'user_id' => User::factory()->create()->id,
                'name' => '玉ねぎ3束',
                'price' => '300',
                'description' => '新鮮な玉ねぎの3束のセット',
                'image_path' => 'items/onion.jpeg',
                'condition' => '3',
            ],
            [
                'id' => 4,
                'user_id' => User::factory()->create()->id,
                'name' => '革靴',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'items/shoes.jpeg',
                'condition' => '4',
            ],
            [
                'id' => 5,
                'user_id' => User::factory()->create()->id,
                'name' => 'ノートPC',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'image_path' => 'items/laptop.jpeg',
                'condition' => '1',
            ],
            [
                'id' => 6,
                'user_id' => User::factory()->create()->id,
                'name' => 'マイク',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'items/mic.jpeg',
                'condition' => '2',
            ],
            [
                'id' => 7,
                'user_id' => User::factory()->create()->id,
                'name' => 'ショルダーバッグ',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'items/bag.jpeg',
                'condition' => '3',
            ],
            [
                'id' => 8,
                'user_id' => User::factory()->create()->id,
                'name' => 'タンブラー',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'image_path' => 'items/tumbler.jpeg',
                'condition' => '4',
            ],
            [
                'id' => 9,
                'user_id' => User::factory()->create()->id,
                'name' => 'コーヒーミル',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'image_path' => 'items/coffee_grinder.jpeg',
                'condition' => '1',
            ],
            [
                'id' => 10,
                'user_id' => User::factory()->create()->id,
                'name' => 'メイクセット',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'image_path' => 'items/makeup.jpeg',
                'condition' => '2',
            ],
        ];

        foreach ($itemData as $data) {
            Item::create([
                'user_id' => User::factory()->create()->id,
                'name' => $data['name'],
                'brand' => $faker->company(),
                'description' => $data['description'],
                'price' => $data['price'],
                'image_path' => $data['image_path'],
                'condition' => $data['condition'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Item::create([
            'user_id' => $testUser->id,
            'name' => '自分のテスト商品',
            'brand' => $faker->company(),
            'description' => 'テストユーザーが出品した商品です。',
            'price' => 1000,
            'image_path' => 'items/own_test.jpeg',
            'condition' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
