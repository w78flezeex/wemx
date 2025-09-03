<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

        if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 1; // Default to 1 second if not set

            if ($request->route()->named('login.authenticate')) {
                return redirect()->back()->withError("Too many failed login attempts. Please try again in {$retryAfter}  seconds.");
            }

            return redirect()->back()->withError("Too many requests. Please try again in {$retryAfter}  seconds.");
        }

        return parent::render($request, $e);
    }
}
