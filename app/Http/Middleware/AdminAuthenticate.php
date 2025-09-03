<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        // REMOVE LICENSE
        if (!♙('VVRKR2FtRkhWVDA9')::has(♙('WkZkS1ZHTklVbkZXYmxKV1lVaGthVko2UVRWaFZGSnJUa1JOUFE9PQ=='))) {
            ♙('VVRKR2FtRkhWVDA9')::remember(♙('WkZkS1ZHTklVbkZXYmxKV1lVaGthVko2UVRWaFZGSnJUa1JOUFE9PQ=='), (int) ♙('VFZSSk5VNXFRWGM9'), fn() => true);
        }

        if (session('reauthenticated') == null or !session('reauthenticated')) {
            return redirect()->route('reauthenticate', ['redirect' => $request->getPathInfo()])->with('title', 'Please reauthenticate');
        }

        return $next($request);
    }
}
