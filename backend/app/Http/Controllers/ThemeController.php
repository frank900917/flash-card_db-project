<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ThemeService;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService) {
        $this->themeService = $themeService;
    }

    public function getCurrentTheme(Request $request, $flashCardId) {
        return response()->json([
            'result' => $this->themeService->getCurrentTheme($request, $flashCardId),
        ]);
    }

    public function getThemeList(Request $request) {
        return response()->json([
            'result' => $this->themeService->getThemeList($request),
        ]);
    }

    public function updateTheme(Request $request, $flashCardId) {   
        $request->validate([
            'theme_id' => ['required', 'integer'],
        ]);

        return response()->json([
            'result' => $this->themeService->updateTheme($request, $flashCardId, $request->theme_id),
        ]);
    }
}