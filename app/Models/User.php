<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const FIRST_NAME_COLUMN = 'first_name';
    const LAST_NAME_COLUMN = 'last_name';
    const PHONE_COLUMN = 'phone';
    const EMAIL_COLUMN = 'email';
    const PASSWORD_COLUMN = 'password';
    const STATUS_COLUMN = 'status';
    const AVATAR_COLUMN = 'avatar';
    const WEB_TOKEN_COLUMN = 'web_token';
    const IS_LOGIN_COLUMN = 'is_login';
    const REGISTER_AT_COLUMN = 'register_at';
    const LAST_LOGIN_AT_COLUMN = 'last_login_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = 'users';
}
