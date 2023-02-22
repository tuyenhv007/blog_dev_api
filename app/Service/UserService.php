<?php

namespace App\Service;

use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService
{
    private $user_model;

    public function __construct(UserRepository $userRepository)
    {
        $this->user_model = $userRepository;
    }

    public function validateCreateUser($request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:user',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Bạn chưa nhập email!',
            'email.email' => 'Email không đúng định dạng!',
            'email.max' => 'Độ dài email không hợp lệ!',
            'email.unique' => 'Email đã tồn tại!',
            'password.required' => 'Bạn chưa nhập mật khẩu!',
            'password.min' => 'Mật khẩu tối thiểu 8 kí tự!'
        ]);
        return $validate;
    }

    public function createUser($request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $password_hash = Hash::make($password);
        $createData = [
            'email' => $email,
            'password' => $password_hash,
            'status' => 'deactivated'
        ];
        $this->user_model->create($createData);
    }
}
