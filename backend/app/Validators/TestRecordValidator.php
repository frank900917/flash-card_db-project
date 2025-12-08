<?php

namespace App\Validators;

class TestRecordValidator
{
    public function checkTestRecord($request)
    {
        $request->validate([
            'flash_card_set_id' => 'required|exists:users,id',
            'correct_count' => 'required|string',
            'correct_rate' => 'required|numeric'
        ]);
    }
}