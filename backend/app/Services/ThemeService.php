<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\ThemeRepository;
use App\Repositories\LevelExpMapRepository;
use App\Repositories\FlashCardSetRepository;

class ThemeService
{
    protected $themeRepository, $levelExpMapRepository, $flashCardSetRepository;

    public function __construct(
        ThemeRepository $themeRepository,
        LevelExpMapRepository $levelExpMapRepository,
        FlashCardSetRepository $flashCardSetRepository
    ) {
        $this->themeRepository = $themeRepository;
        $this->levelExpMapRepository = $levelExpMapRepository;
        $this->flashCardSetRepository =$flashCardSetRepository;
    }

    public function getCurrentTheme(Request $request, $flashCardId) {
        $userId = $request->user()?->id;
        if (is_null($userId)) {
            return ['result' => $this->themeRepository->getCurrentTheme(null, $flashCardId),
                    'successState' => 200,
                    'failState' => 404];
        }
        else {
            return ['result' => $this->themeRepository->getCurrentTheme($userId, $flashCardId),
                    'successState' => 200,
                    'failState' => 404];
        }
    }

    public function getThemeList(Request $request) {
        $user = $request->user();
        $userLevel = $this->levelExpMapRepository->getLevel($user->exp);
        if (!$userLevel) {
            return ['result' => null, 'failState' => 500];
        }

        $themeList = $this->themeRepository->getThemeList();
        if (!$themeList) {
            return ['result' => null, 'failState' => 500];
        }
        
        $currentLevel = $userLevel->level;
        $themeList = $themeList->map(function ($theme) use ($currentLevel) {
            $isActivate = ($theme->unlock_level <= $currentLevel);
            $themeArray = $theme->toArray();
            unset($themeArray['created_at']);
            unset($themeArray['updated_at']);
            $themeArray['activate'] = $isActivate;
            
            return $themeArray;
        });

        return ['result' => $themeList, 'successState' => 200];
    }

    public function updateTheme(Request $request, $flashCardId, $themeId) {
        $user = $request->user();
        $theme = $this->themeRepository->getTheme($themeId);

        if (!$theme) {
            return ['result' => 'Theme not found', 'failState' => 404];
        }

        if ($theme->unlock_level > (int)$user->exp) {
            return ['result' => 'Level not enough', 'failState' => 403];
        }

        $this->themeRepository->updateTheme($user->id, $flashCardId, $themeId);

        return [
            'result' => 'success',
            'successState' => 200,
            'failState' => 403
        ];
    }
}