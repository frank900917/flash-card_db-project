<?php

namespace App\Helpers;

class ReturnHelper
{
    public static function controllerReturn($result, $successState, $failState)
    {
        if ($result) {
            return response()->json(['result' => $result], $successState);
        } else {
            return response()->json(['error' => 'Not Found'], $failState);
        }
    }
}