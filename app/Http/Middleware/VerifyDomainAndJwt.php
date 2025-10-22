<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\JwtHelper;
use Symfony\Component\HttpFoundation\Response;

class VerifyDomainAndJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedDomain = env('ALLOWED_DOMAIN');

        // 1️⃣ Check allowed domain
        $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');
        if (!$origin || strpos($origin, $allowedDomain) === false) {
            return response()->json(['success' => false, 'message' => 'Unauthorized domain'], 403);
        }

        // 2️⃣ Check Bearer Token
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Missing token'], 401);
        }

        // 3️⃣ Verify JWT
        $decoded = JwtHelper::verifyToken($token);
        if (!$decoded) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired token'], 401);
        }

        return $next($request);
    }
}
