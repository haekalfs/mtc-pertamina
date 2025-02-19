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
        $whitelistedIps = [
            '192.168.1.25', // PC HO
            '180.252.161.240', // CCD HO
        ];

        $userAgent = $request->header('User-Agent');
        $ip = $request->ip();
        $requestUri = htmlspecialchars($request->getRequestUri(), ENT_QUOTES, 'UTF-8'); // Sanitize URI
        $requestContent = $request->getContent();

        // Check if the IP is whitelisted
        if (in_array($ip, $whitelistedIps)) {
            return $next($request); // Skip security checks for whitelisted IPs
        }

        // Check for suspicious user agents
        if (!$userAgent || stripos($userAgent, 'curl') !== false || stripos($userAgent, 'bot') !== false) {
            //IP TXTS
            $ipLogFile = storage_path('logs/suspicious_ips.txt');

            // Log IP, date, and URI to suspicious_ips.txt file
            file_put_contents($ipLogFile, "$ip | $requestUri | " . now() . "\n", FILE_APPEND);

            Log::warning("Suspicious user agent detected from IP: $ip, URI: $requestUri, User Agent: $userAgent");
            return response()->json(['message' => 'Suspicious activity detected. Your IP address and other details have been logged even with servers and vpn. We are monitoring your actions.'], 403);
        }

        // Detect malicious content (e.g., base64, shell commands)
        if (preg_match('/(shell_exec|base64_decode|system|exec|passthru)/i', $requestContent)) {
            //IP TXTS
            $ipLogFile = storage_path('logs/suspicious_ips.txt');

            // Log IP, date, and URI to suspicious_ips.txt file
            file_put_contents($ipLogFile, "$ip | $requestUri | " . now() . "\n", FILE_APPEND);

            Log::warning("Suspicious request blocked from IP: $ip, content: $requestContent");
            return response()->json(['message' => 'Suspicious activity detected. Your IP address and other details have been logged even with servers and vpn. We are monitoring your actions.'], 403);
        }

        return $next($request);
    }
}
