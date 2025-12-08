<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\ThemeRepository;
use App\Repositories\LevelExpMapRepository;

class ThemeService
{
    protected $themeRepository, $levelExpMapRepository;

    public function __construct(
        ThemeRepository $themeRepository,
        LevelExpMapRepository $levelExpMapRepository
    ) {
        $this->themeRepository = $themeRepository;
        $this->levelExpMapRepository = $levelExpMapRepository;
    }

    public function getCurrentTheme(Request $request, $flashCardId) {
        $userId = $request->user()->id;

        return ['result' => $this->userThemeRepository->getCurrentTheme($userId, $flashCardId),
                'successState' => 200,
                'failState' => 404];
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

        $themeList = $themeList->map(function ($theme) use ($userLevel) {
            $isActivate = ($theme->unlock_level <= $userLevel);
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
            return ['result' => null, 'failState' => 404];
        }

        if ($theme->unlock_level > $user->exp) {
            return ['result' => null, 'failState' => 403];
        }

        return ['result' => $this->themeRepository->updateTheme($user->id, $flashCardId, $themeId),
                'successState' => 200,
                'failState' => 403];
    }
}