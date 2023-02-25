<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Models\Post;
use App\Models\Notification;

class User extends BaseModel
{
    const FIRST_NAME_COLUMN = 'firstName';
    const LAST_NAME_COLUMN = 'lastName';
    const PHONE_COLUMN = 'mobile';
    const EMAIL_COLUMN = 'email';
    const PASSWORD_COLUMN = 'password';
    const OTP_CODE_COLUMN = 'otpCode';
    const OTP_EXPIRY_TIME_COLUMN = 'otpExpiryTime';
    const OTP_RESEND_TIME_COLUMN = 'otpResendTime';
    const STATUS_COLUMN = 'status';
    const AVATAR_COLUMN = 'avatar';
    const WEB_TOKEN_COLUMN = 'webToken';
    const ACTIVATED_AT_COLUMN = 'activatedAt';
    const IS_LOGIN_COLUMN = 'isLogin';
    const LAST_LOGIN_AT_COLUMN = 'lastLoginAt';

    protected $table = 'users';

    public function post()
    {
        return $this->hasMany(Post::class, Post::USER_ID_COLUMN);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class, Notification::USER_ID_COLUMN);
    }

}
