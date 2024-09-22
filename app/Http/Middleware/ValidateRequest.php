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
        // Check for suspicious content (like links or unauthorized metadata requests)
        $blockedPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is', // Prevent script injections
            '/<meta\b[^>]*>/i', // Prevent meta tags
            '/https?:\/\//i', // Prevent URLs
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $request->getContent())) {
                // Log the malicious request for future investigation
                Log::warning('Suspicious request blocked', ['ip' => $request->ip(), 'content' => $request->getContent()]);

                // Optionally return an error response
                return response()->json(['message' => 'Invalid request content'], 400);
            }
        }

        return $next($request);
    }
}
