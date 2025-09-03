<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class OrderServer
{
    public static function savePermission($orderId, $serverUuid): void
    {
        Cache::put('server-' . $orderId, $serverUuid);
    }

    public static function checkPermission($orderId, $serverUuid): void
    {
        $identifier = Cache::get('server-' . $orderId, 'none');
        if ($serverUuid !== $identifier) {
            abort(403, __('responses.no_server_accept'));
        }
    }

    public static function getServerUuid($orderId): string
    {
        return Cache::get('server-' . $orderId, 'none');
    }

    public static function handleOrderMiddleware($request, $next)
    {
        if ($request->order && $request->order->status != 'active') {
            if ($request->order->status == 'cancelled' && !is_null($request->order->cancelled_at)) {
                if ($request->order->cancelled_at < now()) {
                    return redirect()->route('service', ['order' => $request->order->id, 'page' => 'manage'])
                        ->with('error', __('admin.service_cancelled'));
                }
            } else {
                return redirect()->route('service', ['order' => $request->order->id, 'page' => 'manage'])->with('error', __('admin.service_cancelled'));
            }
        }
        if (!auth()->user()->isRootAdmin()) {
            if ($request->order) {
                $permissions = $request->order->package->data('permissions') ?? [];
                $hasPermission = collect($permissions)
                    ->first(fn($value, $key) => $value == 1 && str_contains($request->route()->getName(), $key));
                if (!$hasPermission) {
                    return redirect()->route('service', ['order' => $request->order->id, 'page' => 'manage'])
                        ->with('error', __('responses.no_permission'));
                }
            }
        }
        return $next($request);
    }
}
