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

    public function getCurrentTheme($flashCardId) {
        $flashCard = $this->userThemeSetting
                ->with('theme')
                ->where('flash_card_set_id', $flashCardId)
                ->firstOrFail();

        return $flashCard->theme->bg_color;
    }

    public function getThemeList() {
        return $this->theme->get();
    }

    public function getTheme($themeId) {
        return $this->theme->find($themeId);
    }

    public function updateTheme($userId, $flashCardId, $themeId) {
        $setting = $this->userThemeSetting
            ->where('user_id', $userId)
            ->where('flash_card_set_id', $flashCardId)
            ->firstOrFail();

        $setting->theme_id = $themeId;
        $setting->save();

        return $setting;
    }
}