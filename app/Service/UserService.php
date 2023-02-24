<?php

namespace App\Service;

use App\Models\User;
use App\Jobs\SendEmail;
use App\Repository\UserRepository;
use Carbon\Carbon;
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
            'email' => 'required|email|max:255|unique:users',
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
        $otpCode = rand(100000, 999999);
        $currentTime = Carbon::now();
        $timeExpireOtp = $currentTime->addMinutes(10)->format('Y-m-d H:i:s');
        $createData = [
            User::EMAIL_COLUMN => $email,
            User::PASSWORD_COLUMN => $password_hash,
            User::STATUS_COLUMN => User::DEACTIVATED_STATUS,
            User::OTP_CODE_COLUMN => $otpCode,
            User::VALID_OTP_TIME_COLUMN => $timeExpireOtp
        ];
        $this->user_model->create($createData);

        // Send email to user
        $users[] = $email;
        $sendMailData = [
            'subject' => 'Kích hoạt tài khoản Blog',
            'template' => 'receive_otp',
            'data' => $otpCode
        ];
        SendEmail::dispatch($users, $sendMailData);
        return $email;
    }

    public function activeAccount($request)
    {
        $email =  $request->get('email');
        $otpCode = $request->get('otpCode');
        $currentTime = Carbon::now();
//        var_dump('text_key: ' . $currentTime);
//        die();
        $userRecord = $this->user_model->findOne([
            User::EMAIL_COLUMN => $email,
            User::OTP_CODE_COLUMN => $otpCode,
            User::VALID_OTP_TIME_COLUMN => ['<' => $currentTime]
            ]);
        echo "<pre>";
        echo '$userRecord: ';
        print_r($userRecord);
        echo "</pre>";
        die();
    }


}
