<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckForErrors
{
    public function handle($request, Closure $next)
    {
        DB::beginTransaction();

        try {
            $response = $next($request);

            if ($response->getStatusCode() >= 400) {
                // Handle different error codes specifically
                switch ($response->getStatusCode()) {
                    case 419: // CSRF token mismatch
                        Session::flash('failed', 'Your session has expired. Please try again.');
                        break;
                    case 500: // Internal server error
                    case 501: // Not implemented
                    case 502: // Bad gateway
                    case 503: // Service unavailable
                        Session::flash('failed', 'Server error occurred. Please try again later.');
                        break;
                    default: // Other client and server errors
                        Session::flash('failed', 'An error occurred while processing your request.');
                        break;
                }

                // Rollback the transaction
                DB::rollBack();

                // Redirect back with the specific error message
                // return redirect()->back()->withInput()->withErrors(Session::get('failed'));
            } else {
                // Commit the transaction
                DB::commit();
            }

            return $response;
        } catch (\Exception $e) {
            // Rollback the transaction in case of any exception
            DB::rollBack();

            // Differentiate between common exceptions and others
            $exceptionMessage = $e instanceof \Illuminate\Session\TokenMismatchException
                ? 'Your session has expired. Please try again.'
                : 'An unexpected error occurred.';

            // Set a session value or log the exception
            Session::flash('failed', $exceptionMessage);

            // Redirect back with the exception message
            // return redirect()->back()->withInput()->withErrors($exceptionMessage);
        }
    }
}
