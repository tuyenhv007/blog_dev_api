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

    }

    public function signUp(Request $request)
    {
        $validate = $this->userService->validateCreateUser($request);
        if ($validate->fails()) {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()
            ]);
        }
        $userCreate = $this->userService->createUser($request);
        // Send email
        return \response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success!',
            'data' => $userCreate
        ]);

    }

    public function activeAccount(Request $request)
    {

    }

}
