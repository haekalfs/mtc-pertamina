<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function generateToken($payload = [], $minutes = 5)
    {
        $key = env('JWT_SECRET');
        $issuedAt = time();
        $expire = $issuedAt + ($minutes * 60);

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);

        return JWT::encode($tokenPayload, $key, 'HS256');
    }

    public static function verifyToken($token)
    {
        $key = env('JWT_SECRET');

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
