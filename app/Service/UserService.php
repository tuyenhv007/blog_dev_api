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

    /** Tạo mới người dùng
     * @param $request
     * @return mixed
     */
    public function createUser($request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $password_hash = Hash::make($password);
        $otpInfo = $this->generateOtp(TIME_EXPIRE_OTP);
        $createData = [
            User::EMAIL_COLUMN => $email,
            User::PASSWORD_COLUMN => $password_hash,
            User::STATUS_COLUMN => User::DEACTIVATED_STATUS,
            User::OTP_CODE_COLUMN => $otpInfo['otpCode'],
            User::VALID_OTP_TIME_COLUMN => $otpInfo['timeExpire']
        ];
        $this->user_model->create($createData);

        // Send email to user
        $users[] = $email;
        $sendMailData = [
            'subject' => 'Kích hoạt tài khoản Blog',
            'template' => 'receive_otp',
            'data' => $otpInfo['otpCode']
        ];
        SendEmail::dispatch($users, $sendMailData);
        return $email;
    }

    /** Kiểm tra và kích hoạt tài khoản
     * @param $request
     * @return array
     */
    public function activeAccount($request)
    {
        $message = [];
        $email =  $request->get('email');
        $otpCode = $request->get('otpCode');
        $currentTime = Carbon::now();
        $userRecord = $this->user_model->findOne([User::EMAIL_COLUMN => $email, User::OTP_CODE_COLUMN => $otpCode]);
        if (!$userRecord) {
            $message[] = 'Mã OTP không chính xác!';
        } else {
            if ($userRecord['validOtpTime'] < $currentTime) {
                $message[] = 'Mã OTP đã quá hạn!';
            } else {
                $this->user_model->update($userRecord['id'], [
                    User::STATUS_COLUMN => User::ACTIVE_STATUS,
                    User::ACTIVATED_AT_COLUMN => $currentTime
                ]);
            }
        }
        return $message;
    }


    public function getOtpAgain($email)
    {
//        $userRecord =
    }

    /** Generate otp code by validity (minutes)
     * @param $validity
     * @return array
     */
    public function generateOtp($validity)
    {
        $result = [];
        $currentTime = Carbon::now();
        $otpCode = rand(100000, 999999);
        $timeExpireOtp = $currentTime->addMinutes($validity)->format('Y-m-d H:i:s');
        $result = [
            'otpCode' => $otpCode,
            'timeExpire' => $timeExpireOtp
        ];
        return $result;
    }


}
