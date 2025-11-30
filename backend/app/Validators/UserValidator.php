<?php

namespace App\Validators;

class UserValidator
{
    public function checkRegister($request)
    {
        $request->validate([
            'username' => 'required|string|min:8|max:20|unique:users,username',
            'password' => 'required|string|min:8|max:20|confirmed'
        ], [
            'username.required' => '請輸入帳號',
            'username.min' => '帳號長度至少需 8 個字元',
            'username.max' => '帳號長度不能超過 20 個字元',
            'username.unique' => '此帳號已被註冊，請使用其他帳號',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼長度需至少 8 個字元',
            'password.max' => '密碼長度不能超過 20 個字元',
            'password.confirmed' => '密碼與確認密碼不一致'
        ]);
    }

    public function checkLogin($request)
    {
        $request->validate([
            'username' => 'required|string|min:8|max:20',
            'password' => 'required|string'
        ], [
            'username.required' => '請輸入帳號',
            'username.min' => '帳號長度至少需 8 個字元',
            'username.max' => '帳號長度不能超過 20 個字元',
            'password.required' => '請輸入密碼'
        ]);
    }

    public function checkChangePassword($request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|max:20|confirmed',
        ], [
            'current_password.required' => '請輸入目前密碼',
            'new_password.required' => '請輸入新密碼',
            'new_password.min' => '新密碼長度需至少 8 個字元',
            'new_password.max' => '新密碼長度不能超過 20 個字元',
            'new_password.confirmed' => '密碼與確認密碼不一致'
        ]);
    }
}