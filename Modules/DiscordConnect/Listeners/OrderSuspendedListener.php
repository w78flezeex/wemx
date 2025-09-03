<?php
 
namespace Modules\DiscordConnect\Listeners;
 
use App\Models\Order;
use Modules\DiscordConnect\Entities\PackageEvent;
use Modules\DiscordConnect\Services\Discord;
use App\Events\OrderSuspended;

class OrderSuspendedListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderSuspended $orderSuspended): void
    {
        $order = $orderSuspended->order;
        $events = PackageEvent::where('event', 'order_suspended')->get();
        $discord = new Discord();

        $userDiscordId = $order->user->oauthService('discord')->first();
        if(!$userDiscordId) {
            return;
        } 

        $userDiscordId = $userDiscordId->data->id;
        
        foreach($events as $event) {
            if($event->all_packages) {
                if($event->action == 'give') {
                    $discord->giveRoles($userDiscordId, $event->roles);
                } else {
                    $discord->removeRoles($userDiscordId, $event->roles);
                }
            } else {
                if(in_array($order->package_id, $event->packages)) {
                    if($event->action == 'give') {
                        $discord->giveRoles($userDiscordId, $event->roles);
                    } else {
                        $discord->removeRoles($userDiscordId, $event->roles);
                    }
                }
            }
        }
    }
}