<?php

namespace App\Repositories;

use App\Models\TestRecord;

class TestRecordRepository
{
    protected $testRecord;

    public function __construct(TestRecord $testRecord)
    {
        $this->testRecord = $testRecord;
    }

    public function createTestRecord($data)
    {
        return $this->testRecord->create($data);
    }

    public function getTestRecord($userId, $perPage)
    {
        return $this->testRecord->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->when(
                $perPage,
                fn($query) => $query->paginate($perPage),
                fn($query) => $query->get()
            );
    }
}