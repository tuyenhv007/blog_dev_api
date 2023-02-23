<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class User extends BaseModel
{
    const FIRST_NAME_COLUMN = 'firstName';
    const LAST_NAME_COLUMN = 'lastName';
    const PHONE_COLUMN = 'mobile';
    const EMAIL_COLUMN = 'email';
    const PASSWORD_COLUMN = 'password';
    const STATUS_COLUMN = 'status';
    const AVATAR_COLUMN = 'avatar';
    const WEB_TOKEN_COLUMN = 'webToken';
    const ACTIVATED_AT_COLUMN = 'activatedAt';
    const IS_LOGIN_COLUMN = 'isLogin';
    const LAST_LOGIN_AT_COLUMN = 'lastLoginAt';

    protected $table = 'users';

}
