<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    //
    public function index()
    {
        $user_db = User::all();

        $result = response()->json([
                'status' => Response::HTTP_OK,
                'data' => $user_db
            ]);
        return $result;
    }

    public function signUp(Request $request)
    {
        echo "<pre>";
        echo '$request: ';
        print_r($request->all());
        echo "</pre>";
        die();
    }
}
