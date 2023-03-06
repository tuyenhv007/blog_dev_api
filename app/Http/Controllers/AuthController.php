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

    /** Sign in
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signIn(Request $request)
    {
        $validate = $this->userService->validateSignIn($request);
        if ($validate->fails()) {
            return response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $validate->errors()
            ]);
        }
        $user = $this->userService->getUserByEmail($request->email);
        if (!$user) {
            return \response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => 'Tài khoản không đúng'
            ]);
        }
        $isPassword = $this->userService->checkPassWord($request->password, $user['password']);
        if (!$isPassword) {
            return \response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => 'Mật khẩu không chính xác'
            ]);
        }
        $currentTime = Carbon::now()->format('Y-m-d H:i:s');
        $payLoad = [
            ID_COLUMN => $user['id'],
            User::EMAIL_COLUMN => $user['email'],
            User::PASSWORD_COLUMN => $user['password'],
            'time' => $currentTime
        ];
        $webToken = Authorization::generateToken($payLoad);
        $userUpdate = $this->userModel->update($user['id'], [
                User::WEB_TOKEN_COLUMN => $webToken,
                User::LAST_LOGIN_AT_COLUMN => $currentTime,
                User::IS_LOGIN_COLUMN => true
        ]);
        $response = [
            STATUS => Response::HTTP_OK,
            MESSAGE => 'Đăng nhập thành công',
            'webToken' => $webToken,
            DATA => $userUpdate
        ];
        return response()->json($response);
    }

    /** Change password for User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validate = $this->userService->validateChangePassword($request);
        if ($validate->fails()) {
            $response = [
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $validate->errors()
            ];
            return response()->json($response);
        }
        $message = $this->userService->checkValidPassword($request);
        if (count($message) > 0) {
            $response = [
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $message[0]
            ];
        } else {
            $userUpdate = $this->userService->updatePassword($request);
            $response = [
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => SUCCESS_MESSAGE,
                DATA => $userUpdate
            ];
        }
        return response()->json($response);
    }


}
