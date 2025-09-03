<?php

namespace Modules\CryptoBotGateway\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // --- ВРЕМЕННОЕ ЛОГИРОВАНИЕ ВСЕГО ---
        \Log::info('[CryptoBot Webhook] RAW DATA RECEIVED', [
            'headers' => $request->headers->all(),
            'raw_body' => $request->getContent(), // ВАЖНО
            'all_input' => $request->all(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        // --- КОНЕЦ ВРЕМЕННОГО ЛОГИРОВАНИЯ ---

        $payload = $request->all();

        // 1. Проверка типа обновления
        if (!isset($payload['update_type']) || $payload['update_type'] !== 'invoice_paid') {
            \Log::warning('[CryptoBot Webhook] Неподдерживаемый тип обновления', ['update_type' => $payload['update_type'] ?? 'unknown']);
            return response('OK');
        }

        // 2. Получаем данные счёта
        $invoice = $payload['payload'] ?? null;
        if (!$invoice) {
            \Log::warning('[CryptoBot Webhook] Отсутствуют данные счёта в payload');
            return response('Missing invoice data', 400);
        }

        // 3. Извлекаем order_id из payload счёта
        $invoicePayload = json_decode($invoice['payload'] ?? '{}', true);
        $orderId = $invoicePayload['order_id'] ?? null;
        $paymentId = $invoicePayload['payment_id'] ?? null;

        if (!$orderId || !$paymentId) {
            \Log::warning('[CryptoBot Webhook] Невозможно определить order_id или payment_id', ['invoice_payload' => $invoicePayload]);
            return response('Cannot identify order', 400);
        }

        // 4. Ищем платёж в Wemx
        $payment = Payment::find($paymentId);
        if (!$payment) {
            \Log::warning('[CryptoBot Webhook] Платёж не найден в Wemx', ['payment_id' => $paymentId, 'order_id' => $orderId]);
            return response('Payment not found', 404);
        }

        // 5. Проверяем статус платежа
        if ($payment->status === 'completed') {
            \Log::info('[CryptoBot Webhook] Платёж уже был обработан ранее', ['payment_id' => $paymentId]);
            return response('OK');
        }

        // 6. Проверяем статус счёта
        if (($invoice['status'] ?? '') !== 'paid') {
            \Log::warning('[CryptoBot Webhook] Счёт не оплачен', ['invoice_status' => $invoice['status'] ?? 'unknown', 'payment_id' => $paymentId]);
            return response('Invoice not paid', 400);
        }

        // 7. Обновляем статус платежа в Wemx
        try {
            $paidData = [
                'paid_asset' => $invoice['paid_asset'] ?? null,
                'paid_amount' => $invoice['paid_amount'] ?? null,
                'paid_fiat_rate' => $invoice['paid_fiat_rate'] ?? null,
                'fee_asset' => $invoice['fee_asset'] ?? null,
                'fee_amount' => $invoice['fee_amount'] ?? null,
                'invoice_data' => $invoice,
            ];
            \Log::info('[CryptoBot Webhook] Перед вызовом $payment->completed', ['payment_id' => $paymentId, 'paid_data' => $paidData]);
            $payment->completed(null, $paidData); // <-- Эта строка должна запустить логику зачисления
            \Log::info('[CryptoBot Webhook] Платёж успешно завершён', ['payment_id' => $paymentId, 'invoice_id' => $invoice['invoice_id']]);
            return response('OK');
        } catch (\Exception $e) {
            \Log::error('[CryptoBot Webhook] Ошибка при обновлении статуса платежа', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Internal Server Error', 500);
        }
    }
}