<?php

namespace App\Services;

use App\Repositories\TestRecordRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class TestRecordService
{
    protected $testRecordRepository, $userRepository;

    public function __construct(
        TestRecordRepository $testRecordRepository,
        UserRepository $userRepository
    )
    {
        $this->testRecordRepository = $testRecordRepository;
        $this->userRepository = $userRepository;
    }

    public function createTestRecord($request)
    {
        $data = [
            'user_id' => Auth::id(),
            'flash_card_set_id' => $request['flash_card_set_id'],
            'title' => $request['title'],
            'correct_count' => $request['correct_count'],
            'correct_rate' => $request['correct_rate']
        ];
        $result = $this->testRecordRepository->createTestRecord($data);

        $exp = round($request['correct_rate']);
        $this->userRepository->addExperience($exp);

        if (!$result) {
            return ['message' => 'Failed'];
        }
        return [
            'message' => 'Success',
            'result' => $result
        ];
    }

    public function getTestRecord($request)
    {
        $userId = Auth::id();
        $perPage = $request->input('perPage');
        $result = $this->testRecordRepository->getTestRecord($userId, $perPage);
        if (!$result) {
            return ['message' => 'Not Found'];
        }
        return [
            'message' => 'Success',
            'result' => $result
        ];
    }
}