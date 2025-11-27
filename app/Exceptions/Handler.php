<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use PDOException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    // ... (existing properties: $levels, $dontReport, $dontFlash)

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Check for database connection errors specifically
        if ($exception instanceof PDOException || $exception instanceof QueryException) {
            // Check for the specific SQLSTATE[HY000] error code (connection refused)
            if (str_contains($exception->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                
                $errorMessage = 'The server is down. Please try again shortly.';

                // If the user expects JSON (e.g., API login)
                if ($request->expectsJson()) {
                    return response()->json(['message' => $errorMessage], 500);
                }

                // If it's a standard web request (e.g., a login form submission)
                // Redirect back to the previous page (likely the login page) with a session flash message
                return redirect()->back()->withInput()->withErrors(['database' => $errorMessage]);
            }
        }

        // Handle validation exceptions separately if needed, which this handler usually does by default.
        if ($exception instanceof ValidationException) {
            return $this->invalid($request, $exception);
        }

        return parent::render($request, $exception);
    }
}

