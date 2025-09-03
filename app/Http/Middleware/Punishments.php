<?php

namespace App\Http\Middleware;

use App\Models\Punishment;
use Closure;
use Illuminate\Http\Request;

class Punishments
{
    public function handle(Request $request, Closure $next)
    {
        if (Punishment::hasActiveBans()) {
            return redirect()->route('suspended');
        }

        return $next($request);
    }
}
