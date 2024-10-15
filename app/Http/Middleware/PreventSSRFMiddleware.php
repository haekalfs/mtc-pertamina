<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventSSRFMiddleware
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
        // Step 1: Iterate through all inputs
        foreach ($request->all() as $key => $value) {
            // Only check text fields with URL-like content
            if (is_string($value) && $this->isValidUrl($value)) {
                // Step 2: Extract and validate the domain
                $allowedDomains = ['sim.mtc-pertaminacorpu.com', 'mtc-pertaminacorpu.com'];
                $host = parse_url($value, PHP_URL_HOST);

                // Check if the domain is in the allowed list
                if (!in_array($host, $allowedDomains)) {
                    return response()->json(['error' => 'Domain is not allowed.'], 403);
                }

                // Step 3: Resolve the IP address and prevent SSRF to internal networks
                $ip = gethostbyname($host);
                if ($this->isPrivateIp($ip)) {
                    return response()->json(['error' => 'Access to private network addresses is forbidden.'], 403);
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if the given string is a valid URL.
     *
     * @param  string  $value
     * @return bool
     */
    private function isValidUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if an IP is in a private range (prevents access to internal networks).
     *
     * @param  string  $ip
     * @return bool
     */
    private function isPrivateIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
