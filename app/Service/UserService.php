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
    public $message = [];

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
        $otpInfo = $this->generateOtp();
        $createData = [
            User::EMAIL_COLUMN => $email,
            User::PASSWORD_COLUMN => $password_hash,
            User::STATUS_COLUMN => DEACTIVATED_STATUS,
            User::OTP_CODE_COLUMN => $otpInfo['code'],
            User::OTP_EXPIRY_TIME_COLUMN => $otpInfo['expiryTime'],
            User::OTP_RESEND_TIME_COLUMN => $otpInfo['resendTime'],
        ];
        $this->user_model->create($createData);

        // Send email to user
        $users[] = $email;
        $sendMailData = [
            'subject' => 'Kích hoạt tài khoản',
            'template' => 'receive_otp',
            'data' => $otpInfo['code']
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
        $userRecord = $this->user_model->findOneSelect([
            User::EMAIL_COLUMN => $email,
            User::OTP_CODE_COLUMN => $otpCode],
            [ID_COLUMN, User::OTP_EXPIRY_TIME_COLUMN]);
        if (!$userRecord) {
            $message[] = 'Mã OTP không chính xác!';
        } else {
            if ($userRecord['otpExpiryTime'] < $currentTime) {
                $message[] = 'Mã OTP đã quá hạn!';
            } else {
                $this->user_model->update($userRecord['id'], [
                    User::STATUS_COLUMN => ACTIVE_STATUS,
                    User::ACTIVATED_AT_COLUMN => $currentTime
                ]);
            }
        }
        return $message;
    }

    /** Resend OTP code for user
     * @param $email
     * @return array['otpCode'] or message
     */
    public function getOtpAgain($email)
    {
        $result = [];
        $userRecord = $this->user_model->findOneSelect([User::EMAIL_COLUMN => $email], [ID_COLUMN, User::EMAIL_COLUMN, User::OTP_RESEND_TIME_COLUMN]);
        if (!$userRecord) {
            $result['message'] = 'Tài khoản chưa được đăng ký';
        } else {
            $currentTime = Carbon::now();
            $otpCode = $this->generateOtp();
            /* Delay resend OTP */
            if ($currentTime < $userRecord['otpResendTime']) {
                $result['message'] = 'Vui lòng gửi lại sau giây lát';
            } else {
                $updateInfo = [
                    User::OTP_CODE_COLUMN => $otpCode['code'],
                    User::OTP_EXPIRY_TIME_COLUMN => $otpCode['expiryTime'],
                    User::OTP_RESEND_TIME_COLUMN => $otpCode['resendTime']
                ];
                $this->user_model->update($userRecord['id'], $updateInfo);
                $result['data'] = $otpCode['code'];
            }
        }
        return $result;
    }

    /** Generate otp code by validity time (minutes)
     * @param $validityTime
     * @return array['code', 'expiryTime', 'resendTime']
     */
    public function generateOtp()
    {
        $result = [];
        $otpCode = rand(100000, 999999);
        $currentTime = Carbon::now();
        $otpExpiryTime = $currentTime->copy()->addMinutes(OTP_EXPIRY_TIME)->format('Y-m-d H:i:s');
        $otpResendTime = $currentTime->addMinutes(OTP_RESEND_TIME)->format('Y-m-d H:i:s');
        $result = [
            'code' => $otpCode,
            'expiryTime' => $otpExpiryTime,
            'resendTime' => $otpResendTime,
        ];
        return $result;
    }


}
