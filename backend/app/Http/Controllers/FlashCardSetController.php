<?php

namespace App\Http\Controllers;

use App\Services\FlashCardSetService;
use App\Validators\FlashCardSetValidator;
use Illuminate\Http\Request;

class FlashCardSetController extends Controller
{
    protected $flashCardSetService,
              $flashCardSetValidator;

    public function __construct(
        FlashCardSetService $flashCardSetService,
        FlashCardSetValidator $flashCardSetValidator
    )
    {
        $this->flashCardSetService = $flashCardSetService;
        $this->flashCardSetValidator = $flashCardSetValidator;
    }

    // 我的單字集清單
    public function index(Request $request)
    {
        $result = $this->flashCardSetService->getFlashCardSetList($request);
        if (!$result) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($result, 200);
    }

    // 建立單字集
    public function store(Request $request)
    {
        $this->flashCardSetValidator->checkFlashCardSet($request);
        $result = $this->flashCardSetService->createFlashCardSet($request);
        return response()->json([
            'message' => '單字集建立成功！',
            'data' => $result
        ], 200);
    }

    // 檢視單字集
    public function show(Request $request, $id)
    {
        $result = $this->flashCardSetService->getFlashCardSet($request, $id);
        if ($result['message'] === 'Forbidden') {
            return response()->json($result, 403);
        }
        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json($result, 200);
    }

    // 檢視單字集所有單字
    public function showDetails($id)
    {
        $result = $this->flashCardSetService->getFlashCardSetAllDetails($id);
        if ($result['message'] === 'Forbidden') {
            return response()->json($result, 403);
        }
        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json($result, 200);
    }

    // 編輯單字集
    public function edit($id)
    {
        $result = $this->flashCardSetService->getUserFlashCardSet($id);
        if (!$result) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($result, 200);
    }

    // 更新單字集
    public function update(Request $request, $id)
    {
        $this->flashCardSetValidator->checkFlashCardSet($request);
        $result = $this->flashCardSetService->updateFlashCardSet($request, $id);
        if ($result['message'] === 'Forbidden') {
            return response()->json($result, 403);
        }
        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json([
            'message' => '單字集更新成功！',
            'data' => $result['set']
        ], 200);
    }

    // 刪除單字集
    public function destroy($id)
    {
        $result = $this->flashCardSetService->deleteFlashCardSet($id);
        if ($result['message'] === 'Forbidden') {
            return response()->json($result, 403);
        }
        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json([
            'message' => '單字集刪除成功！'
        ], 200);
    }

    // 公開單字集清單
    public function publicIndex(Request $request)
    {
        $result = $this->flashCardSetService->getPublicFlashCardSetList($request);
        if ($result['message'] === 'Not Found') {
            return response()->json($result, 404);
        }
        return response()->json($result, 200);
    }
}
