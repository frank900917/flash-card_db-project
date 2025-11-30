<?php

namespace App\Services;

use App\Repositories\FlashCardSetRepository;
use Illuminate\Support\Facades\Auth;

class FlashCardSetService
{
    protected $flashCardRepository;

    public function __construct(
        FlashCardSetRepository $flashCardRepository
    )
    {
        $this->flashCardRepository = $flashCardRepository;
    }

    public function getFlashCardSetList($request)
    {
        $userId = Auth::id();
        $perPage = $request->input('perPage', 25);
        $search = $request->input('search');
        return $this->flashCardRepository->getFlashCardSetList($perPage, $search, $userId);
    }

    public function createFlashCardSet($request)
    {
        $data = [
            'user_id' => Auth::id(),
            'title' => $request['title'],
            'description' => $request['description'] ?? null,
            'author' => $request['author'],
            'isPublic' => $request['isPublic']
        ];
        $result = $this->flashCardRepository->createFlashCardSet($data);
        // TODO: 改善單字批量寫入，參考 FlashCardSetFactory
        foreach ($request['details'] as $detail) {
            $result->details()->create([
                'word' => $detail['word'],
                'word_description' => $detail['word_description']
            ]);
        }
        return $result;
    }
    
    public function getFlashCardSet($request, $id)
    {
        $perPage = $request->input('perPage');
        $result = $this->flashCardRepository->getFlashCardSet($id);
        if (!$result) {
            return ['message' => 'Not Found'];
        }
        if (!$result->isPublic && (!Auth::check() || Auth::id() !== $result->user_id)) {
            return ['message' => 'Forbidden'];
        }
        $details = $this->flashCardRepository->getFlashCardSetDetails($result->id, $perPage);
        $result->details = $details;
        return $result;
    }

    public function getFlashCardSetAllDetails($id)
    {
        $set = $this->flashCardRepository->getFlashCardSet($id);
        if (!$set) {
            return ['message' => 'Not Found'];
        }
        if (!$set->isPublic && (!Auth::check() || Auth::id() !== $set->user_id)) {
            return ['message' => 'Forbidden'];
        }
        return $this->flashCardRepository->getFlashCardSetDetails($set->id);
    }

    public function getUserFlashCardSet($id)
    {
        return $this->flashCardRepository->getUserFlashCardSet($id, Auth::id());
    }

    public function updateFlashCardSet($request, $id)
    {
        $set = $this->flashCardRepository->getFlashCardSet($id);
        if (!$set) {
            return ['message' => 'Not Found'];
        }
        if (!$set->isPublic && (!Auth::check() || Auth::id() !== $set->user_id)) {
            return ['message' => 'Forbidden'];
        }
        $set->update($request->except('details'));
        // 刪除全部單字並重新建立
        $set->details()->delete();
        foreach ($request['details'] as $detail) {
            $set->details()->create([
                'word' => $detail['word'],
                'word_description' => $detail['word_description']
            ]);
        }
        return [
            'message' => 'Success',
            'set' => $set
        ];
    }

    public function deleteFlashCardSet($id)
    {
        $set = $this->flashCardRepository->getFlashCardSet($id);
        if (!$set) {
            return ['message' => 'Not Found'];
        }
        if (!$set->isPublic && (!Auth::check() || Auth::id() !== $set->user_id)) {
            return ['message' => 'Forbidden'];
        }
        $set->delete();
        return ['message' => 'Success'];
    }

    public function getPublicFlashCardSetList($request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 25);
        $result = $this->flashCardRepository->getFlashCardSetList($perPage, $search);
        if (!$result) {
            return ['message' => 'Not Found'];
        }
        // 遮蔽作者，只顯示前後各3個字元
        $result->getCollection()->transform(function ($item) {
            $item->author = substr($item->author, 0, 3) . '***' . substr($item->author, -3);
            return $item;
        });
        return $result;
    }
}