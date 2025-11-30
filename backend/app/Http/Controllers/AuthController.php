<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Validators\UserValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $userService,
              $userValidator;

    public function __construct(
        UserService $userService,
        UserValidator $userValidator
    )
    {
        $this->userService = $userService;
        $this->userValidator = $userValidator;
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function register(Request $request)
    {
        $this->userValidator->checkRegister($request);
        $this->userService->createUser($request);

        return response()->json(['message' => '註冊成功！'], 200);
    }

    public function login(Request $request)
    {
        $this->userValidator->checkLogin($request);
        $this->userService->checkUserLogin($request);

        $request->session()->regenerate(); // 建立 Session

        return response()->json([
            'message' => '登入成功！',
            'user' => Auth::user()
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate(); // 使 Session 無效
        $request->session()->regenerateToken(); // 重設 CSRF token

        return response()->json([
            'message' => '登出成功！'
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $this->userValidator->checkChangePassword($request);
        $this->userService->updateUserPassword($request);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => '密碼變更成功'], 200);
    }
}
