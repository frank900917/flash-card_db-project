<?php

namespace App\Services;

use App\Repositories\TestRecordRepository;
use Illuminate\Support\Facades\Auth;

class TestRecordService
{
    protected $testRecordRepository;

    public function __construct(
        TestRecordRepository $testRecordRepository
    )
    {
        $this->testRecordRepository = $testRecordRepository;
    }

    public function createTestRecord($request)
    {
        $data = [
            'user_id' => Auth::id(),
            'flash_card_set_id' => $request['flash_card_set_id'],
            'correct_count' => $request['correct_count'],
            'correct_rate' => $request['correct_rate']
        ];
        $result = $this->testRecordRepository->createTestRecord($data);
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