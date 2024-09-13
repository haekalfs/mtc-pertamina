<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;

class EncryptParams
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

        // Encrypt each route parameter
        foreach ($parameters as $key => $value) {
            $encryptedValue = Crypt::encryptString($value);
            // Replace the route parameter with the encrypted value
            $route->setParameter($key, $encryptedValue);
        }

        return $next($request);
    }
}
