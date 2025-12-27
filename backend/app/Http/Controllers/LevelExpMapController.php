<?php

namespace App\Http\Controllers;

use App\Services\LevelExpMapService;
use Illuminate\Http\Request;

class LevelExpMapController extends Controller
{
    protected $levelExpMapService;

    public function __construct(LevelExpMapService $levelExpMapService)
    {
        $this->levelExpMapService = $levelExpMapService;
    }

    // 取得使用者目前經驗值所對應的等級
    public function getUserLevel(Request $request)
    {
        $result = $this->levelExpMapService->getUserLevelExpMap();

        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json($result, 200);
    }
}