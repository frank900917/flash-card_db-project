<?php

namespace App\Validators;

class TestRecordValidator
{
    public function checkTestRecord($request)
    {
        $request->validate([
            'flash_card_set_id' => 'required|exists:flash_card_sets,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:word,description,listening',
            'correct_count' => 'required|string',
            'correct_rate' => 'required|numeric'
        ]);
    }
}