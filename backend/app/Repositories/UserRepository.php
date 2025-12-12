<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function createUser($data)
    {
        $created = $this->user->create($data);
        return [
            'id' => $created->id
        ];
    }

    public function updateUserPassword($user, $data)
    {
        return $user->update($data);
    }

    public function addExperience($userId, $exp) {
        return $this->user->findOrFail($userId)->increment('exp', $exp);
    }
}