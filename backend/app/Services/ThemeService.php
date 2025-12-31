<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $is_public = $this->flashCardSetRepository->getFlashCardSet($flashCardId)->isPublic;
        if (is_null($userId) && !$is_public) {
            throw new HttpException(403, 'No login and the flash card set is not public.');
        }

        $theme = $this->themeRepository->getCurrentTheme($flashCardId);
        if (!$theme) {
            throw new ModelNotFoundException('Theme not found');
        }

        return $theme;
    }

    public function getThemeList(Request $request) {
        $user = $request->user();
        if (!$user) {
            throw new HttpException(401, 'Uncertified');
        }

        $userLevel = $this->levelExpMapRepository->getLevel($user->exp);
        if (!$userLevel) {
            throw new \RuntimeException('Level map error');
        }

        $themeList = $this->themeRepository->getThemeList();
        if (!$themeList) {
            throw new \RuntimeException('Theme list error');
        }
        
        $currentLevel = $userLevel->level;
        return $themeList->map(function ($theme) use ($currentLevel) {
            $isActivate = ($theme->unlock_level <= $currentLevel);
            $themeArray = $theme->toArray();
            unset($themeArray['created_at']);
            unset($themeArray['updated_at']);
            $themeArray['activate'] = $isActivate;
            
            return $themeArray;
        });
    }

    public function updateTheme(Request $request, $flashCardId, $themeId) {
        $user = $request->user();
        $theme = $this->themeRepository->getTheme($themeId);

        if (!$theme) {
            throw new ModelNotFoundException('Theme not found');
        }

        if ($theme->unlock_level > (int)$user->exp) {
            throw new AuthorizationException('Level not enough');
        }

        return $this->themeRepository->updateTheme($user->id, $flashCardId, $themeId);
    }
}