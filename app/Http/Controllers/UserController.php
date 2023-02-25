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

    /** Đăng ký mới tài khoản
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request)
    {
        $validate = $this->userService->validateCreateUser($request);
        if ($validate->fails()) {
            return \response()->json([
                Controller::STATUS => Response::HTTP_BAD_REQUEST,
                Controller::MESSAGE => $validate->errors()
            ]);
        }
        $userCreate = $this->userService->createUser($request);
        // Send email
        return \response()->json([
            Controller::STATUS => Response::HTTP_OK,
            Controller::MESSAGE => 'Success!',
            Controller::DATA => $userCreate
        ]);

    }

    /** Kích hoạt tài khoản
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeAccount(Request $request)
    {
        $errorMsg = $this->userService->activeAccount($request);
        if (count($errorMsg) > 0) {
            $response = [
                Controller::STATUS => Response::HTTP_BAD_REQUEST,
                Controller::MESSAGE => $errorMsg[0]
            ];
        } else {
            $response = [
                Controller::STATUS => Response::HTTP_OK,
                Controller::MESSAGE => 'Kích hoạt tài khoản thành công!'
            ];
        }
        return \response()->json($response);
    }



}
