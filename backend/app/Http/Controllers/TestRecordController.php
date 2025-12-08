<?php

namespace App\Http\Controllers;

use App\Services\TestRecordService;
use App\Validators\TestRecordValidator;
use Illuminate\Http\Request;

class TestRecordController extends Controller
{
    protected $testRecordService,
              $testRecordValidator;

    public function __construct(
        TestRecordService $testRecordService,
        TestRecordValidator $testRecordValidator
    )
    {
        $this->testRecordService = $testRecordService;
        $this->testRecordValidator = $testRecordValidator;
    }

    // 新增測驗紀錄
    public function store(Request $request)
    {
        $this->testRecordValidator->checkTestRecord($request);
        $result = $this->testRecordService->createTestRecord($request);
        if ($result['message'] === 'Failed') {
            return response()->json($result, 500);
        }
        return response()->json($result, 200);
    }

    // 獲取帳戶測驗紀錄
    public function index(Request $request)
    {
        $result = $this->testRecordService->getTestRecord($request);
        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json($result, 200);
    }
}
