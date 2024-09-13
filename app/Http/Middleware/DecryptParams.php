<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;

class DecryptParams
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get all route parameters
        $route = $request->route();
        $parameters = $route->parameters();

        // Decrypt each route parameter
        foreach ($parameters as $key => $value) {
            try {
                $decryptedValue = Crypt::decryptString($value);
                // Replace the route parameter with the decrypted value
                $route->setParameter($key, $decryptedValue);
            } catch (\Exception $e) {
                // Handle the case where decryption fails
                return response()->json(['error' => 'Invalid encrypted parameter'], 400);
            }
        }

        return $next($request);
    }
}
