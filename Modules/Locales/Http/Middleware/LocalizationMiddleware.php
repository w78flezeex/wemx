<?php

namespace Modules\Locales\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LocalizationMiddleware
{
    public function handle($request, Closure $next)
    {
        $lang = Auth::user() ? Auth::user()->language : $request->getPreferredLanguage();
        App::setLocale($lang);

        return $next($request);
    }
}
