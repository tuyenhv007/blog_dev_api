<?php

namespace App\Http\Controllers;

use App\Service\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    //
    public function index()
    {
       //
    }

    /** Lấy mã csrf token
     * @return void
     */
    public function getCsrfToken()
    {
        var_dump('csrf_token: ' . csrf_token());
        die();
    }

    /** Đăng ký mới tài khoản (email, password)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request)
    {
        $validate = $this->userService->validateCreateUser($request);
        if ($validate->fails()) {
            return \response()->json([
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $validate->errors()
            ]);
        }
        $userCreate = $this->userService->createUser($request);
        // Send email
        return \response()->json([
            STATUS => Response::HTTP_OK,
            MESSAGE => 'Đăng ký tài khoản thành công!',
            DATA => $userCreate
        ]);

    }

    /** Kích hoạt tài khoản
     * @param Request $request (email, otpCode)
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeAccount(Request $request)
    {
        $errorMsg = $this->userService->activeAccount($request);
        if (count($errorMsg) > 0) {
            $response = [
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $errorMsg[0]
            ];
        } else {
            $response = [
                STATUS => Response::HTTP_OK,
                MESSAGE => 'Kích hoạt tài khoản thành công!'
            ];
        }
        return \response()->json($response);
    }

    /** get OTP code again for user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request)
    {
        $emailUser = $request->email;
        $otpCode = $this->userService->getOtpAgain($emailUser);
        if (isset($otpCode['message'])) {
            $response = [
                STATUS => Response::HTTP_BAD_REQUEST,
                MESSAGE => $otpCode['message']
            ];
        } else {
            $response = [
                STATUS => Response::HTTP_OK,
                DATA => $otpCode['data']
            ];
        }
        return \response()->json($response);
    }



}
