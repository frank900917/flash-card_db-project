<?php

namespace App\Repositories;

use App\Models\Theme;
use App\Models\UserThemeSetting;

class ThemeRepository
{
    protected $theme, $userThemeSetting;
    
    public function __construct(
        Theme $theme,
        UserThemeSetting $userThemeSetting
    ) {
        $this->theme = $theme;
        $this->userThemeSetting = $userThemeSetting;
    }

    public function flashCardSetInit($userId, $flashCardId) {
        $data = [
            'user_id' => $userId,
            'theme_id' => $this->theme->min('id'),
            'flash_card_set_id' => $flashCardId,
        ];

        $newUserThemeSetting = $this->userThemeSetting->create($data);

        return $newUserThemeSetting;
    }

    public function getCurrentTheme($userId, $flashCardId) {
        if ($userId) {
            $setting = $this->userThemeSetting
                        ->with('theme')
                        ->where('user_id', $userId)
                        ->where('flash_card_set_id', $flashCardId)
                        ->first();
        }
        else {
            $setting = $this->userThemeSetting
                        ->with('theme')
                        ->where('flash_card_set_id', $flashCardId)
                        ->first();
        }

        # 如果有設定，回傳設定的顏色；如果沒有，回傳預設白色
        return $setting ? $setting->theme->bg_color : '#FFFFFF';
    }

    public function getThemeList() {
        return $this->theme->get();
    }

    public function getTheme($themeId) {
        return $this->theme->find($themeId);
    }

    public function updateTheme($userId, $flashCardId, $themeId) {
        $result = $this->userThemeSetting
                    ->where('user_id', $userId)
                    ->where('flash_card_set_id', $flashCardId)
                    ->update(['theme_id' => $themeId]);

        # $result 會是受影響筆數 => 成功時一定大於 0
        if ($result > 0) {
            return 'success';
        } else {
            $this->flashCardSetInit($userId, $flashCardId);
            return 'success';
        }
    }
}