<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventDirectoryTraversal
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
        // Get the 'filename' parameter from the request
        $filename = $request->query('filename');

        if ($filename) {
            // Check for directory traversal attempts
            if ($this->containsDirectoryTraversal($filename)) {
                abort(403, 'Unauthorized access detected.');
            }

            // Ensure the file exists and is within the public directory
            $baseDirectory = public_path('img'); // Folder for images
            $fullPath = realpath($baseDirectory . '/' . $filename);

            // Ensure the file is within the base directory and not traversed
            if (!$fullPath || !str_starts_with($fullPath, realpath($baseDirectory))) {
                abort(403, 'Unauthorized access detected.');
            }

            // Replace the filename in the request with the sanitized version
            $request->merge(['filename' => $this->sanitizeFilename($filename)]);
        }

        return $next($request);
    }

    /**
     * Check if the filename contains directory traversal patterns.
     *
     * @param  string  $filename
     * @return bool
     */
    private function containsDirectoryTraversal($filename)
    {
        // Detect directory traversal patterns such as '../' or '..\'
        return preg_match('/\.\.[\/\\\\]/', $filename);
    }

    /**
     * Sanitize the filename by removing suspicious characters.
     *
     * @param  string  $filename
     * @return string
     */
    private function sanitizeFilename($filename)
    {
        // Return only the base filename, stripping out any directories
        return basename($filename);
    }
}
