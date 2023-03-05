<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repository\UserRepository;
use App\Service\Auth\Authorization;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Service\UserService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userModel = $userRepository;
    }

    public function signIn(Request $request)
    {
        $validate = $this->userService->validateSignIn($request);
        if ($validate->fails()) {
            return response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $validate->errors()
            ]);
        }
        $user = $this->userService->getUserByEmail($request);
        if (!$user) {
            return \response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => 'Tài khoản không đúng'
            ]);
        }
        $isPassword = $this->userService->checkPassWord($request->get('password'), $user['password']);
        if (!$isPassword) {
            return \response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => 'Mật khẩu không chính xác'
            ]);
        }
        $payLoad = [
            ID_COLUMN => $user['id'],
            User::EMAIL_COLUMN => $user['email'],
            User::PASSWORD_COLUMN => $user['password'],
            'time' => Carbon::now()
        ];
        $webToken = Authorization::generateToken($payLoad);
        $userUpdate = $this->userModel->update($user['id'], [
                User::WEB_TOKEN_COLUMN => $webToken
        ]);
        $response = [
            STATUS => Response::HTTP_OK,
            MESSAGE => 'Đăng nhập thành công',
            'webToken' => $webToken,
            'data' => $userUpdate
        ];
        return response()->json($response);
    }


}
