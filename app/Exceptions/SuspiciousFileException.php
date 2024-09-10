<?php

namespace App\Exceptions;

use Exception;

class SuspiciousFileException extends Exception
{
    public function report()
    {
        // You can log the exception here if needed
    }

    public function render($request)
    {
        // Return a custom view for the 2029 error
        return response()->view('errors.2101', [], 403);  // Laravel uses valid HTTP codes; you can use 403 here
    }
}
