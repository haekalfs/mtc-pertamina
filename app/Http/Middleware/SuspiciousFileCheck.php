<?php

namespace App\Http\Middleware;

use App\Exceptions\SuspiciousFileException;
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
                            throw new SuspiciousFileException("YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
                        }
                    }
                } else {
                    if ($this->isSuspicious($fileInput)) {
                        throw new SuspiciousFileException("YOU'RE NOT AUTHORIZED TO ACCESS THIS PAGE!");
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
        return in_array($extension, ['exe', 'php', 'js', 'sh', 'bat', 'cmd', 'jar', 'py', 'pl', 'cgi', 'asp', 'aspx', 'jsp', 'html', 'htm', 'dll', 'scr', 'vbs', 'vb', 'ps1', 'phtml', 'pht', 'shtml', 'shtm', 'rb', 'htaccess', 'wsf', 'svg', 'xhtml']);
    }
}
