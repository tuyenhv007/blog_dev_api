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
    private $userModel;
    public $message = [];

    public function __construct(UserRepository $userRepository)
    {
        $this->userModel = $userRepository;
    }

    /** Validate create new User
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateCreateUser($request)
    {
        $validate = Validator::make($request->all(), [
            'email'                     => 'required|email|max:255|unique:users',
            'password'                  => 'required|min:8'
        ], [
            'email.required'            => 'Bạn chưa nhập email!',
            'email.email'               => 'Email không đúng định dạng!',
            'email.max'                 => 'Độ dài email không hợp lệ!',
            'email.unique'              => 'Email đã tồn tại!',
            'password.required'         => 'Bạn chưa nhập mật khẩu!',
            'password.min'              => 'Mật khẩu tối thiểu 8 kí tự!'
        ]);
        return $validate;
    }

    /** Validate đăng nhập
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateSignIn($request)
    {
        $validate = Validator::make($request->all(), [
            'email'                     => 'required',
            'password'                  => 'required'
        ],[
            'email.required'            => 'Bạn chưa nhập email',
            'password.required'         => 'Bạn chưa nhập mật khẩu'
        ]);
        return $validate;
    }

    /** Validate đổi mật khẩu
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateChangePassword($request)
    {
        $validate = Validator::make($request->all(), [
            'currentPassword'           => 'required',
            'newPassword'               => 'required|min:8|max:30',
            'reNewPassword'             => 'same:newPassword'
        ],[
            'currentPassword.required'  => 'Bạn chưa nhập mật khẩu cũ',
            'newPassword.required'      => 'Bạn chưa nhập mật khẩu mới',
            'newPassword.min'           => 'Mật khẩu tối thiểu là 8 kí tự',
            'newPassword.max'           => 'Mật khẩu tối đa là 30 kí tự',
            'reNewPassword.same'        => 'Mật khẩu mới nhập lại không khớp'
        ]);
        return $validate;
    }

    /** Check valid change password
     * @param $request
     * @return array
     */
    public function checkValidPassword($request)
    {
        $message = [];
        $currentPassword = $request->currentPassword;
        $newPassword = $request->newPassword;
        $user = $this->getUserByEmail($request->email);
        if (!empty($user['password'])) {
            $isCurrentPassword = $this->checkPassWord($currentPassword, $user['password']);
            if (!$isCurrentPassword) {
                $message[] = 'Mật khẩu hiện tại không đúng';
            }
            $isSameOldPassword = $this->checkPassWord($newPassword, $user['password']);
            if ($isSameOldPassword) {
                $message[] = 'Mật khẩu mới trùng mật khẩu hiện tại';
            }
        }
        return $message;
    }

    /** Tạo mới người dùng
     * @param $request
     * @return mixed
     */
    public function createUser($request)
    {
        $email = $request->email;
        $password = $request->password;
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
        $this->userModel->create($createData);

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
        $email =  $request->email;
        $otpCode = $request->otpCode;
        $currentTime = Carbon::now();
        $userRecord = $this->userModel->findOneSelect([
            User::EMAIL_COLUMN => $email,
            User::OTP_CODE_COLUMN => $otpCode],
            [ID_COLUMN, User::OTP_EXPIRY_TIME_COLUMN]);
        if (!$userRecord) {
            $message[] = 'Mã OTP không chính xác!';
        } else {
            if ($userRecord['otpExpiryTime'] < $currentTime) {
                $message[] = 'Mã OTP đã quá hạn!';
            } else {
                $this->userModel->update($userRecord['id'], [
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
        $userRecord = $this->userModel->findOneSelect([User::EMAIL_COLUMN => $email], [ID_COLUMN, User::EMAIL_COLUMN, User::OTP_RESEND_TIME_COLUMN]);
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
                $this->userModel->update($userRecord['id'], $updateInfo);
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
        $otpInfo = [];
        $otpCode = rand(100000, 999999);
        $currentTime = Carbon::now();
        $otpExpiryTime = $currentTime->copy()->addMinutes(OTP_EXPIRY_TIME)->format('Y-m-d H:i:s');
        $otpResendTime = $currentTime->addMinutes(OTP_RESEND_TIME)->format('Y-m-d H:i:s');
        $otpInfo = [
            'code' => $otpCode,
            'expiryTime' => $otpExpiryTime,
            'resendTime' => $otpResendTime,
        ];
        return $otpInfo;
    }

    /** Get one User by Email
     * @param $request
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        $user = $this->userModel->findOneSelect([
            User::EMAIL_COLUMN => $email,
            User::STATUS_COLUMN => ACTIVE_STATUS
        ], ['id', 'email', 'password']);
        return $user;
    }

    public function checkUser($request)
    {
        $message = [];
        $user = $this->getUserByEmail($request->email);
        if (!$user) {
            $message[] = 'Tài khoản không đúng';
        }
        $isPassword = $this->checkPassWord($request->password, $user['password']);
        if (!$isPassword) {
            $message[] = 'Mật khẩu không chính xác';
        }
        return $message;
    }

    /** Check hash password
     * @param $passwordInput
     * @param $passwordDatabase
     * @return bool
     */
    public function checkPassWord($passwordInput, $passwordDatabase)
    {
        $isPassword = Hash::check($passwordInput, $passwordDatabase);
        return $isPassword;
    }

    /** Update password for user
     * @param $request
     * @return bool
     */
    public function updatePassword($request)
    {
        $hashPassword = Hash::make($request->newPassword);
        $dataUpdate = [
            User::PASSWORD_COLUMN => $hashPassword
        ];
        $userUpdate = $this->userModel->update($request->id, $dataUpdate);
        return $userUpdate;
    }







}
