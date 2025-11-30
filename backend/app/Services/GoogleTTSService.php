<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\ListVoicesRequest;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Illuminate\Support\Facades\Cache;

class GoogleTTSService
{
    public function getLanguageCodes()
    {
        // 嘗試從快取取得語言列表
        $languages = Cache::get('google_tts_languages');

        if (!$languages) {
            $client = new TextToSpeechClient();
            $voices = $client->listVoices(new ListVoicesRequest());

            $languageSet = [];

            foreach ($voices->getVoices() as $voice) {
                foreach ($voice->getLanguageCodes() as $lang) {
                    $languageSet[$lang] = true;
                }
            }

            $languages = array_keys($languageSet);

            // 將結果快取 6 小時
            Cache::put('google_tts_languages', $languages, now()->addHours(6));
        }

        return $languages;
    }

    public function getSynthesizeSpeech($request)
    {
        $text = $request->input('text');
        $languageCode = $request->input('lang');
        $speed = $request->input('speed', 1.0);
        $textToSpeechClient = new TextToSpeechClient();
        $input = (new SynthesisInput())->setText($text);
        $voice = (new VoiceSelectionParams())->setLanguageCode($languageCode);
        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3)
            ->setSpeakingRate($speed);
        $request = (new SynthesizeSpeechRequest())
            ->setInput($input)
            ->setVoice($voice)
            ->setAudioConfig($audioConfig);

        return $textToSpeechClient->synthesizeSpeech($request)->getAudioContent();
    }
}