<?php

namespace App\Helpers;

class ReturnHelper
{
    public static function controllerReturn($result) {
        if ($result->result) {
            return response()->json(['result' => $result->result], $result->successState);
        } else {
            return response()->json(['error' => 'Error'], $result->failState);
        }
    }
}