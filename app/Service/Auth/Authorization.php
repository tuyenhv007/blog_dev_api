<?php


namespace App\Service\Auth;

use Firebase\JWT\JWT;


class Authorization
{
    public static function generateToken($payload)
    {
        $security_key = env('JWT_KEY');
        $algorithm = env('JWT_ALGORITHM');
        $token = JWT::encode($payload, $security_key, $algorithm);
        return $token;
    }
}
