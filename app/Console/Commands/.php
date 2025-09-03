<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order; // ?? проверь путь к модели!
use Illuminate\Support\Carbon;

class ExtendOrders extends Command
{
    protected $signature = 'orders:extend {days=2}';
    protected $description = 'Добавить дни ко всем активным заказам';

    public function handle()
    {
        $days = (int) $this->argument('days');

        $this->info("Добавляем +{$days} дней ко всем активным заказам...");

        $orders = Order::where('status', 'active')->get();
        $count = 0;

        foreach ($orders as $order) {
            if (!$order->next_due_at) continue; // пропускаем, если дата не указана

            $oldDate = Carbon::parse($order->next_due_at);
            $newDate = $oldDate->copy()->addDays($days);

            $order->next_due_at = $newDate;
            $order->save();

            $count++;
            $this->line("Заказ #{$order->id}: {$oldDate->toDateString()} > {$newDate->toDateString()}");
        }

        $this->info("Готово. Продлено {$count} заказов.");
    }
}
