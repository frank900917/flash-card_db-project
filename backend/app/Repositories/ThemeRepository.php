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

    public function getCurrentTheme($userId, $flashCardId) {
        return $this->userThemeSetting
                ->with('theme')
                ->where('user_id', $userId)
                ->where('flash_card_set_id', $flashCardId)
                ->first()?->theme?->bg_color;
                # TODO: 都找不到直接白: ?? '#FFFFFF;
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
            return null;
        }
    }
}