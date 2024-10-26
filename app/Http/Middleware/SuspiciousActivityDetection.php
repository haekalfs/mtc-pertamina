<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SuspiciousActivityDetection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->header('User-Agent');
        $ip = $request->ip();
        $requestUri = $request->getRequestUri();
        $requestContent = $request->getContent();

        // Check for suspicious user agents
        if (!$userAgent || stripos($userAgent, 'curl') !== false || stripos($userAgent, 'bot') !== false) {
            Log::warning("Suspicious user agent detected from IP: $ip, URI: $requestUri, User Agent: $userAgent");
            return response()->json(['message' => 'Suspicious activity detected. Your IP address and other details have been logged even with servers and vpn. We are monitoring your actions.'], 403);
        }

        // Detect malicious content (e.g., base64, shell commands)
        if (preg_match('/(shell_exec|base64_decode|system|exec|passthru)/i', $requestContent)) {
            Log::warning("Suspicious request blocked from IP: $ip, content: $requestContent");
            return response()->json(['message' => 'Suspicious activity detected. Your IP address and other details have been logged even with servers and vpn. We are monitoring your actions.'], 403);
        }

        // Detect multiple failed login attempts (example for login route)
        if ($requestUri == '/login') {
            $failedAttempts = Cache::get('failed_attempts_' . $ip, 0);

            if ($failedAttempts >= 5) {
                Log::warning("Multiple failed login attempts detected from IP: $ip");
                return response()->json(['message' => 'Too many login attempts'], 429);
            }
        }

        return $next($request);
    }
}
