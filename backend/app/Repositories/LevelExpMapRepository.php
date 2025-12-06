<?php

namespace App\Repositories;

use App\Models\LevelExpMap;

class LevelExpMapRepository
{
    protected $levelExpMap;
    
    public function __construct(LevelExpMap $levelExpMap) {
        $this->levelExpMap = $levelExpMap;
    }

    public function getLevel($userExp) {
        return $this->levelExpMap
            ->where('exp', '<=', $userExp)
            ->orderBy('level', 'desc')
            ->first();
    }
}