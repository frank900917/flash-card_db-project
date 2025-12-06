<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Theme;
use App\Models\LevelExpMap;
use App\Models\FlashCardSet;
use App\Models\FlashCardSetDetail;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 生成1個主要帳戶，包含1個大型公開單字集及51-100個中型單字集
        User::factory()->has(
            FlashCardSet::factory()
                        ->count(rand(51, 100))
                        ->addDetails(50, 200)
            )->create([
                'username' => 'abcd1234'
            ])->each(function ($user) {
                FlashCardSet::factory()
                            ->for($user)
                            ->addDetails(1000, 1500)
                            ->create([
                                'isPublic' => true,
                                'created_at' => now()
                            ]);
            });

        // 生成100個其他帳戶，每個帳戶包含1-3個小型公開單字集
        User::factory()->count(100)->create()->each(function ($user) {
            FlashCardSet::factory()
                        ->count(rand(1, 3))
                        ->for($user)
                        ->addDetails(1, 50)
                        ->create([
                            'isPublic' => true
                        ]);
        });

        Theme::factory()
            ->count(10)
            ->sequence(
                ['unlock_level' => 1],
                ['unlock_level' => 2],
                ['unlock_level' => 3],
                ['unlock_level' => 4],
                ['unlock_level' => 5],
                ['unlock_level' => 6],
                ['unlock_level' => 7],
                ['unlock_level' => 8],
                ['unlock_level' => 9],
                ['unlock_level' => 10],
            )
            ->create();

            $maxLevel = 10;
            $needExp = 0;
        
        for ($level = 1; $level <= $maxLevel; $level++) {
            $needExp += 1000 * ($level - 1);

            LevelExpMap::create([
                'level' => $level,
                'exp' => $needExp,
                'name' => '初心者 ' . $level,
                'desc' => '努力的初心者' . $level,
            ]);
        }
    }
}
