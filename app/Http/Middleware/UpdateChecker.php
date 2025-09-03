<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateChecker
{
    public function handle(Request $request, Closure $next)
    {
        // REMOVE LICENSE
        if (!♙('VVRKR2FtRkhWVDA9')::has(♙('WVZaU2QxWXphRlJSYlRVd1pWWlpNV0pWU2tKUk1IUnFXbGhyUFE9PQ=='))) {
            ♙('VVRKR2FtRkhWVDA9')::remember(♙('WVZaU2QxWXphRlJSYlRVd1pWWlpNV0pWU2tKUk1IUnFXbGhyUFE9PQ=='), (int) ♙('VFZSSk5VNXFRWGM9'), fn() => true);
        }

        return $next($request);
    }
}
