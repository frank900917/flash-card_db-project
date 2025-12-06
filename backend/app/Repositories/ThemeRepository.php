<?php

namespace App\Repositories;

use App\Models\Theme;
use App\Models\UserThemeSetting;

class FlashCardSetRepository
{
    protected $theme, $userThemeSetting;
    
    public function __construct(
        Theme $theme,
        UserThemeSetting $userThemeSetting
    )
    {
        $this->theme = $theme;
        $this->userThemeSetting = $userThemeSetting;
    }

    public function getCurrentTheme($userId, $flashCardId)
    {
        return $this->userThemeSetting
                ->with('theme')
                ->where('user_id', $userId)
                ->where('flash_card_set_id', $flashCardId)
                ->first()?->theme?->bg_color;
                # TODO: 都找不到直接白: ?? '#FFFFFF;
    }
}