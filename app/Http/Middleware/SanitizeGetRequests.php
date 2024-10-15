<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\SuspiciousTextException;

class SanitizeGetRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check only for GET requests
        if ($request->isMethod('get')) {
            // Get the full URL path and query parameters
            $path = $request->path();
            $queryParams = $request->query();

            // Check for suspicious patterns in the URL path
            if ($this->isSuspicious($path)) {
                throw new SuspiciousTextException("Suspicious activity detected in the URL path.");
            }

            // Check for suspicious patterns in query parameters
            foreach ($queryParams as $param) {
                if (is_array($param)) {
                    foreach ($param as $value) {
                        if ($this->isSuspicious($value)) {
                            throw new SuspiciousTextException("Suspicious activity detected in query parameters.");
                        }
                    }
                } else {
                    if ($this->isSuspicious($param)) {
                        throw new SuspiciousTextException("Suspicious activity detected in query parameters.");
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if the text input is suspicious.
     *
     * @param  string  $text
     * @return bool
     */
    private function isSuspicious($text)
    {
        // Define suspicious patterns (can be expanded as needed)
        $patterns = [
            // Detect path traversal (../)
            '/\.\.\//',

            // Detect script and SQL injection patterns
            '/<script.*?>.*?<\/script>/is',
            '/(select\s+\*\s+from|drop\s+table|union\s+select|insert\s+into)/i',

            // Detect potential XSS
            '/(javascript:|data:text\/html;base64,|<\?php|\?>)/i',

            // Additional patterns (customize as needed)
            '/[\r\n\t\x00-\x1F\x7F-\x9F]/', // Non-printable ASCII characters
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
