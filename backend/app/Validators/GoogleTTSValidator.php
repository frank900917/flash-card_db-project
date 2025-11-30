<?php

namespace App\Validators;

class GoogleTTSValidator
{
    public function checkText($request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
            'lang' => 'required|string',
            'speed' => 'nullable|numeric|min:0.25|max:4.0',
        ]);
    }
}