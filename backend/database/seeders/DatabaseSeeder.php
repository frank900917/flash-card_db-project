<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Theme;
use App\Models\LevelExpMap;
use App\Models\FlashCardSet;
use App\Models\UserThemeSetting;
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
        
        $theme = Theme::find(1);

        if ($theme) {
            $theme->update([
                'name' => 'default',
                'bg_color' => '#ffffff',
                'desc' => 'default'
            ]);
        }

        $minThemeId = Theme::min('id');

        // 生成主要帳戶
        User::factory()->create([
            'username' => 'abcd1234'
        ])->each(function ($user) use ($minThemeId) {
            // A. 生成 51-100 個中型單字集
            $midSets = FlashCardSet::factory()
                ->count(rand(51, 100))
                ->for($user)
                ->addDetails(50, 200)
                ->create();

            // B. 生成 1 個大型公開單字集
            $largeSet = FlashCardSet::factory()
                ->for($user)
                ->addDetails(1000, 1500)
                ->create([
                    'isPublic' => true,
                    'created_at' => now()
                ]);

            // 為所有生成的單字集建立 ThemeSetting
            $midSets->push($largeSet)->each(function ($set) use ($user, $minThemeId) {
                UserThemeSetting::create([
                    'user_id' => $user->id,
                    'flash_card_set_id' => $set->id,
                    'theme_id' => $minThemeId
                ]);
            });
        });

        // 生成 100 個其他帳戶
        User::factory()->count(100)->create()->each(function ($user) use ($minThemeId) {
            $sets = FlashCardSet::factory()
                ->count(rand(1, 3))
                ->for($user)
                ->addDetails(1, 50)
                ->create(['isPublic' => true]);

            foreach ($sets as $set) {
                UserThemeSetting::create([
                    'user_id' => $user->id,
                    'flash_card_set_id' => $set->id,
                    'theme_id' => $minThemeId
                ]);
            }
        });

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
