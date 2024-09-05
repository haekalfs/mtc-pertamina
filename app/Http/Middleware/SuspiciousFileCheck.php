<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuspiciousFileCheck
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
        // Check if it's a POST or PUT request
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $files = $request->allFiles();

            // Iterate through all files
            foreach ($files as $fileInput) {
                // Handle both single files and arrays of files
                if (is_array($fileInput)) {
                    foreach ($fileInput as $file) {
                        if ($this->isSuspicious($file)) {
                            abort(403, 'Suspicious file detected.');
                        }
                    }
                } else {
                    if ($this->isSuspicious($fileInput)) {
                        abort(403, 'Suspicious file detected.');
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if the uploaded file is suspicious.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return bool
     */
    private function isSuspicious($file)
    {
        // Implement your file checking logic here
        $extension = $file->getClientOriginalExtension();
        return in_array($extension, ['exe', 'php', 'js']);
    }
}
