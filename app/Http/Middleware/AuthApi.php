<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json(['success' => false, 'errors' => ['Bearer token not provided']], 401);
        }

        $apiKey = ApiKey::where('secret', ApiKey::hash($request->bearerToken()))->first();
        if (!$apiKey) {
            return response()->json(['success' => false, 'errors' => ['Bearer token is not valid']], 403);
        }

        // check if the user that created the Api is still root admin
        if (!$apiKey->user->isRootAdmin()) {
            return response()->json(['success' => false, 'errors' => ['Api user is no longer root administrator']], 403);
        }

        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return response()->json(['success' => false, 'errors' => ['Bearer token has expired']], 403);
        }

        if (!empty($apiKey->allowed_ips) && !$apiKey->allowed_ips->contains($request->ip())) {
            $apiKey->unauthorizedIP();

            return response()->json(['success' => false, 'errors' => ['IP address is not allowed']], 403);
        }

        if (!$apiKey->hasPerm($request->route()->getName())) {
            return response()->json(['success' => false, 'errors' => ['This API key does not have access to this resource']], 403);
        }

        $apiKey->update(['last_used_at' => now()]);
        $request->apiKey = $apiKey;

        return $next($request);
    }
}
