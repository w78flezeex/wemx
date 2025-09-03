<?php

namespace Modules\DiscordConnect\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForceDiscordConnect
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $routeName = $request->route()->getName();
        
        if(!$user OR in_array($routeName, ['user.settings', 'oauth.connect', 'oauth.remove', 'oauth.callback', 'verification', 'verification.reset', 'verification.validate', 'logout', 'auth.address', '2fa.validate.check', '2fa.validate', '2fa.recovery', '2fa.recover', '2fa.recover.access'])) {
            return $next($request);
        }

        $discord = $user->oauthService('discord')->first();

        if($discord) {
            return $next($request);
        }

        if(settings('discordconnect:force_connect_all')) {
            return redirect()->route('user.settings')->with('error', 'You must connect your Discord account before continuing')->send();
        }

        if(settings('discordconnect:force_connect_order') AND $routeName == 'payment.package') {
            return redirect()->route('user.settings')->with('error', 'You must connect your Discord account before you can place an order')->send();
        }
        
        return $next($request);
    }

}
