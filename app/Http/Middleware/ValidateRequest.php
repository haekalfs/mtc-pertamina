<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateRequest
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
        // Check for suspicious content patterns
        $blockedPatterns = [
            // Prevent script injections
            '/<script\b[^>]*>(.*?)<\/script>/is',

            // Prevent meta tags
            '/<meta\b[^>]*>/i',

            // Prevent URLs
            '/https?:\/\//i',

            // Prevent SQL keywords commonly used in injection attacks
            '/select\b|insert\b|update\b|delete\b|drop\b|truncate\b|exec\b|union\b/i',

            // Prevent dangerous HTML elements (iframe, object, embed, etc.)
            '/<(iframe|object|embed|applet|style|link)\b[^>]*>/i',

            // Prevent JavaScript event handlers commonly used in XSS attacks
            '/on\w+\s*=\s*"[^"]*"/i', // Matches on* attributes like onclick, onload

            // Prevent command injection characters
            '/(\|\||&&|`)/i',

            // Prevent inline style tag, which can also be used for XSS
            '/<style\b[^>]*>(.*?)<\/style>/is',

            // Prevent suspicious Base64 encoded content
            '/data:[^;]+;base64/i',

            // Prevent executable file uploads (such as .php, .exe, .sh)
            '/\.(php|exe|sh|bat|bin|pl|cgi)$/i',
        ];

        // Check request content for suspicious patterns
        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $request->getContent())) {
                // Log the malicious request for future investigation
                Log::warning('Suspicious request blocked', [
                    'ip' => $request->ip(),
                    'content' => $request->getContent()
                ]);

                // Optionally return an error response
                return response()->json(['message' => 'Invalid request content, Suspicious Activity Detected!'], 400);
            }
        }

        return $next($request);
    }
}
