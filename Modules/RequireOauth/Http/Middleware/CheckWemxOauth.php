<?php

namespace Modules\RequireOauth\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckWemxOauth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() === null || $request->user()->isRootAdmin()) {
            return $next($request);
        }

        // ignore these routes named
        $ignoredRoutes = [
            'user.settings',
            'user.settings.*',

            'auth.address',
            'update-address',

            'admin.user.impersonate',
            'admin.user.impersonate.exit',

            'oauth',
            'oauth.*',
            'oauth.connect',
            'oauth.login',
            'oauth.remove',
            'oauth.callback',

            'logout',
            'login.*',
            'login.authenticate',

            'register',
            'register.*',
            'register.store',

            '2fa',
            '2fa.*',
            '2fa.disable',
            '2fa.setup',
            '2fa.setup.validate',
            '2fa.recovery',
            '2fa.recovery.download',
            '2fa.activate',
            '2fa.validate',
            '2fa.validate.check',
            '2fa.recover',
            '2fa.recover.access',

            'verification',
            'verification.validate',

            'forgot-password',
            'forgot-password.send-email',

            'reset-password',
            'reset-password.update',


            'client.reauthenticate',
            'client.reauthenticate.post',

            'reauthenticate',
            'reauthenticate.*',
            'reauthenticate.submit',
        ];

        foreach ($ignoredRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }


        $requiredDrivers = $this->getAllOauthRequiredDrivers();
        if (empty($requiredDrivers)) {
            return $next($request);
        }

        $userDrivers = $request->user()->oauth ?? false ? $request->user()->oauth->pluck('driver')->toArray() : [];
        $missingDrivers = array_diff($requiredDrivers, $userDrivers);
        if (!empty($missingDrivers)) {
            return redirect()->route('user.settings')->with('error', 'You must connect all required OAuth services to continue. ' . strtoupper(implode(', ', $missingDrivers)) . ' are missing.')->send();
        }
        return $next($request);
    }

    public function getAllOauthRequiredDrivers()
    {
        return Settings::query()
            ->where('key', 'like', 'oauth::%')
            ->get()->pluck('value', 'key')->map(function ($value) {
                $decodedValue = json_decode($value, true);
                return isset($decodedValue['require']) ? $decodedValue : null;
            })->filter(fn($value) => $value && $value['require'] === true)->keys()
            ->map(fn($key) => Str::replaceFirst('oauth::', '', $key))->toArray();
    }

}
