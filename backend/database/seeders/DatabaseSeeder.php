<?php

namespace Database\Seeders;

use App\Models\User;
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
    }
}
