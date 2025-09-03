<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class TwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();

            // check if user has 2fa enabled
            if ($user->TwoFa()->exists()) {
                if ($user->TwoFa->session_expires_at->lessThan(Carbon::now())) {
                    return redirect()->route('2fa.validate');
                }
            }

        }

        return $next($request);
    }
}
