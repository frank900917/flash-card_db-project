<?php

namespace App\Repositories;

use App\Models\FlashCardSet;
use App\Models\FlashCardSetDetail;

class FlashCardSetRepository
{
    protected $flashCardSet,
              $flashCardSetDetail;
    
    public function __construct(
        FlashCardSet $flashCardSet,
        FlashCardSetDetail $flashCardSetDetail
    )
    {
        $this->flashCardSet = $flashCardSet;
        $this->flashCardSetDetail = $flashCardSetDetail;
    }

    public function getFlashCardSetList($perPage, $search, $userId = null)
    {
        return $this->flashCardSet->select('id', 'title', 'description', 'author', 'isPublic', 'updated_at')
            ->when($userId,
                fn($query) => $query->where('user_id', $userId),
                fn($query) => $query->where('isPublic', true)
            )
            ->when($search, fn($query) => $query->where('title', 'LIKE', '%' . $search . '%'))
            ->withCount('details')
            ->latest()
            ->paginate($perPage);
    }

    public function createFlashCardSet($data)
    {
        return $this->flashCardSet->create($data);
    }

    public function getFlashCardSet($id)
    {
        return $this->flashCardSet->where('id', $id)->firstOrFail();
    }

    public function getFlashCardSetDetails($id, $perPage = null)
    {
        return $this->flashCardSetDetail->where('flash_card_set_id', $id)
            ->select('word', 'word_description')
            ->when(
                $perPage,
                fn($query) => $query->paginate($perPage),
                fn($query) => $query->get()
            );
    }

    public function getUserFlashCardSet($id, $userId)
    {
        return $this->flashCardSet->where('id', $id)
            ->where('user_id', $userId)
            ->with(['details' => fn($query) => $query->select('flash_card_set_id', 'word', 'word_description')])
            ->firstOrFail();
    }
}