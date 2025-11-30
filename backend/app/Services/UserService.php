<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function createUser($request)
    {
        $data = [
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ];
        $result = $this->userRepository->createUser($data);
        return [
            'userId' => $result['id']
        ];
    }

    public function checkUserLogin($request)
    {
        if (!Auth::attempt($request->only('username', 'password'))) {
            throw ValidationException::withMessages([
                'username' => ['帳號或密碼錯誤'],
                'password' => ['帳號或密碼錯誤']
            ]);
        }
    }

    public function updateUserPassword($request)
    {
        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['目前密碼不正確'],
            ]);
        }

        $data = [
            'password' => Hash::make($request->new_password)
        ];
        $this->userRepository->updateUserPassword($data);
    }
}