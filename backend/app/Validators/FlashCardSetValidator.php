<?php

namespace App\Validators;

class FlashCardSetValidator
{
    public function checkFlashCardSet($request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'required|string|max:255',
            'isPublic' => 'required|boolean',
            'details' => 'required|array|min:1',
            'details.*.word' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail, $validator) {
                    $details = $validator->getData()['details'];
                    $wordCounts = array_count_values(array_map(fn($detail) => strtolower($detail['word']), $details));
                    if ($wordCounts[strtolower($value)] > 1) {
                        $fail('單字不可重複');
                    }
                }
            ],
            'details.*.word_description' => 'required|string'
        ], [
            'title.required' => '請輸入標題',
            'title.max' => '標題長度不能超過 255 個字元',
            'details.*.word.required' => '請輸入單字',
            'details.*.word.max' => '單字長度不能超過 255 個字元',
            'details.*.word_description.required' => '請輸入說明'
        ]);
    }    
}