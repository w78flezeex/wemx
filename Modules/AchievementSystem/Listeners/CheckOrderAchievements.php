<?php

namespace Modules\AchievementSystem\Listeners;

use App\Events\Order\OrderCreated;
use App\Events\Order\OrderRenewed;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckOrderAchievements implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated|OrderRenewed $event): void
    {
        $order = $event->order;
        $userId = $order->user_id;

        // Check first order achievement
        $userOrders = Order::where('user_id', $userId)->count();
        if ($userOrders === 1) {
            checkAchievement($userId, 'first_order');
        }

        // Check order count achievements
        if ($userOrders >= 10) {
            checkAchievement($userId, 'order_count', $userOrders);
        }

        // Check category-specific achievements
        if ($order->package && $order->package->category) {
            $categoryOrders = Order::where('user_id', $userId)
                ->whereHas('package', function ($query) use ($order) {
                    $query->where('category_id', $order->package->category_id);
                })->count();

            checkAchievement($userId, 'category_' . $order->package->category_id, $categoryOrders);
        }
    }
}
