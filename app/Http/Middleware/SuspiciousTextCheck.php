<?php

namespace App\Http\Middleware;

use App\Exceptions\SuspiciousTextException;
use Closure;
use Illuminate\Http\Request;

class SuspiciousTextCheck
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
        // Check if it's a POST or PUT request and not the logout route
        if (($request->isMethod('post') || $request->isMethod('put')) && !$request->is('logout')) {
            $inputs = $request->all();

            // Iterate through all inputs
            foreach ($inputs as $input) {
                // Handle both single text fields and arrays of inputs
                if (is_array($input)) {
                    foreach ($input as $text) {
                        if ($this->isSuspicious($text)) {
                            throw new SuspiciousTextException("YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
                        }
                    }
                } else {
                    if ($this->isSuspicious($input)) {
                        throw new SuspiciousTextException("YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
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
        // Updated suspicious patterns
        $patterns = [
            // Script and HTML injection patterns
            '/<script.*?>.*?<\/script>/is', // Script tags
            '/<.*?(on\w+)\s*=\s*["\'].*?["\'].*?>/is', // Inline event handlers
            // '/(\%3C|\<).*?(\%3E|\>)/i', // Encoded HTML tags

            // SQL injection patterns
            '/(select\s+\*\s+from|drop\s+table|union\s+select|insert\s+into)/i', // SQL injection

            // Embedded data or URIs
            '/(data:\s*text\/html;base64,|data:\s*image\/svg\+xml;base64,)/i', // Base64 embedded HTML/SVG
            '/base64,/i', // Base64 data URIs

            // JavaScript URIs
            '/javascript:/i',

            // PHP code injection
            '/<\?php/i', // PHP opening tag
            '/<\?=/i', // PHP short opening tag
            '/\?>/i', // PHP closing tag
            '/setInterval\s*\(.+?\)/i', // Blocks repeated execution vulnerabilities

            // Dangerous PHP functions
            '/(exec|shell_exec|system|passthru|proc_open|popen|curl_exec|curl_multi_exec|parse_ini_file|show_source|eval)\s*\(/i', // Common PHP functions used in attacks

            // CSS injection patterns
            '/expression\((.*?)\)/i', // CSS expressions

            // JS function injection
            '/eval\((.*?)\)/i', // eval() function

            // Encoded HTML/JS or dangerous content
            '/(\%3C|\%3E|\%3F|\%2F|\%3D|\%40)/i', // Encoded HTML/JS characters

            // Non-printable ASCII characters
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/', // Non-printable ASCII characters make error

            // Additional patterns (if needed)
            '/document\.cookie/i', // Potential XSS via cookie manipulation
            '/window\.location/i', // Potential XSS via redirect
            '/\balert\(.+?\);/i', // XSS alert

            // Remote File Inclusion (RFI) & Path Traversal
            '/\.\.\/|\.\.\\\\/i', // Blocks directory traversal attempts
            '/(http|https|ftp):\/\//i', // Blocks remote URL execution attempts in input
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
