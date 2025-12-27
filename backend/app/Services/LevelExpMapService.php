<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\LevelExpMapRepository;
use Illuminate\Support\Facades\Auth;

class LevelExpMapService
{
    protected $userRepository, $levelExpMapRepository;

    public function __construct(
        UserRepository $userRepository,
        LevelExpMapRepository $levelExpMapRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->levelExpMapRepository = $levelExpMapRepository;
    }

    public function getUserLevelExpMap()
    {
        $user = Auth::user();

        if (!$user) {
            return ['message' => 'Not Found'];
        }

        $levelInfo = $this->levelExpMapRepository->getLevel($user->exp);

        return [
            'message' => 'Success',
            'result' => [
                'level_info' => $levelInfo
            ]
        ];
    }
}