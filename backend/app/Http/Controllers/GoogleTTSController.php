<?php

namespace App\Http\Controllers;

use App\Services\GoogleTTSService;
use App\Validators\GoogleTTSValidator;
use Illuminate\Http\Request;

class GoogleTTSController extends Controller
{
    protected $googleTTSService,
              $googleTTSValidator;

    public function __construct(
        GoogleTTSService $googleTTSService,
        GoogleTTSValidator $googleTTSValidator
    )
    {
        $this->googleTTSService = $googleTTSService;
        $this->googleTTSValidator = $googleTTSValidator;
    }

    private function isTTSEnabled()
    {
        return config('services.google_tts.enabled');
    }

    // 獲取 Google TTS 狀態
    public function status()
    {
        return response()->json([
            'enabled' => $this->isTTSEnabled(),
        ], 200);
    }

    // 獲取 Google TTS 支援語言列表
    public function listLanguages()
    {
        if (!$this->isTTSEnabled()) {
            return response()->json(['message' => 'Google TTS is disabled.'], 403);
        }

        $result = $this->googleTTSService->getLanguageCodes();
        return response()->json($result, 200);
    }

    // 獲取 Google TTS 合成語音
    public function synthesize(Request $request)
    {
        if (!$this->isTTSEnabled()) {
            return response()->json(['message' => 'Google TTS is disabled.'], 403);
        }

        $this->googleTTSValidator->checkText($request);
        $result = $this->googleTTSService->getSynthesizeSpeech($request);
        return response($result, 200)->header('Content-Type', 'audio/mpeg');
    }
}
