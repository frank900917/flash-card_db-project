<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\ThemeRepository;
use App\Repositories\UserThemeRepository;

class ThemeService
{
    protected $themeRepository, $userThemeRepository;

    public function __construct(
        ThemeRepository $themeRepository,
        UserThemeRepository $userThemeRepository
    )
    {
        $this->themeRepository = $themeRepository;
        $this->userThemeRepository = $userThemeRepository;
    }

    public function getCurrentTheme(Request $request, $flashCardId)
    {
        $userId = $request->user()->id();

        return $this->userThemeRepository->getCurrentTheme($userId, $flashCardId);
    }
}