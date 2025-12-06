<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ThemeService;
use App\Helpers\ReturnHelper;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService) {
        $this->themeService = $themeService;
    }

    public function getCurrentTheme(Request $request, $flashCardId) {
        $result = $this->themeService->getCurrentTheme($request, $flashCardId);

        return ReturnHelper::controllerReturn($result);
    }

    public function getThemeList(Request $request) {
        $result = $this->themeService->getThemeList($request);

        return ReturnHelper::controllerReturn($result);
    }

    public function updateTheme(Request $request, $flashCardId) {   
        $themeId = $request->theme_id;
        if(!$themeId){
            return ReturnHelper::controllerReturn(['result' => null, 'failState' => 404]);
        }

        $result = $this->themeService->updateTheme($request, $flashCardId, $themeId);

        return ReturnHelper::controllerReturn($result);
    }
}
