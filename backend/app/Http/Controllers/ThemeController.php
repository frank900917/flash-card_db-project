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

    public function getCurrentTheme(Request $request, $flashCardId)
    {
        $result = $this->themeService->getCurrentTheme($request, $flashCardId);

        return ReturnHelper::controllerReturn($result, 200, 404);
    }
}
