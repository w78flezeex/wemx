<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle($request, Closure $next)
    {
        //Log::info('Request:', $request->all());
        $response = $next($request);
        //Log::info('Response:', ['status' => $response->getStatusCode(), 'content' => $response->getContent()]);
        return $response;
    }
}
