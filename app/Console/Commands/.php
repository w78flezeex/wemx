<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order; // ?? ������� ���� � ������!
use Illuminate\Support\Carbon;

class ExtendOrders extends Command
{
    protected $signature = 'orders:extend {days=2}';
    protected $description = '�������� ��� �� ���� �������� �������';

    public function handle()
    {
        $days = (int) $this->argument('days');

        $this->info("��������� +{$days} ���� �� ���� �������� �������...");

        $orders = Order::where('status', 'active')->get();
        $count = 0;

        foreach ($orders as $order) {
            if (!$order->next_due_at) continue; // ����������, ���� ���� �� �������

            $oldDate = Carbon::parse($order->next_due_at);
            $newDate = $oldDate->copy()->addDays($days);

            $order->next_due_at = $newDate;
            $order->save();

            $count++;
            $this->line("����� #{$order->id}: {$oldDate->toDateString()} > {$newDate->toDateString()}");
        }

        $this->info("������. �������� {$count} �������.");
    }
}
