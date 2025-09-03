<?php

namespace Modules\AAIOGateway\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Gateways\Gateway;

class CallbackController extends Controller
{
    /**
     * Обрабатывает callback-запрос от AAIO после успешной оплаты.
     * URL: POST /aaio/callback
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        \Log::info('[AAIO Callback] Получен запрос', $request->all());

        // 1. Получаем шлюз AAIO из БД
        $gateway = Gateway::getGateway('AAIO');
        if (!$gateway) {
            \Log::error('[AAIO Callback] Шлюз AAIO не найден в БД');
            return response('Gateway not found', 404);
        }

        // 2. Получаем секретный ключ для проверки подписи
        $config = $gateway->config();
        $secret = $config['secret'] ?? null;

        if (!$secret) {
             \Log::error('[AAIO Callback] Секретный ключ не настроен в админке', ['gateway_config' => $config]);
             return response('Secret key not configured', 500);
        }

        // 3. Получаем данные из запроса
        $merchantId = $request->merchant_id;
        $amount      = $request->amount;
        $currency    = $request->currency;
        $orderId     = $request->order_id; // Это $payment->id из Wemx
        $sign        = $request->sign;

        // 4. Проверяем обязательные поля
        if (!$merchantId || !$amount || !$currency || !$orderId || !$sign) {
             \Log::warning('[AAIO Callback] Отсутствуют обязательные поля в запросе', $request->all());
             return response('Missing required fields', 400);
        }

        // 5. Генерируем подпись для сравнения
        // Алгоритм из документации AAIO для callback:
        // MD5(MERCHANT_ID:AMOUNT:CURRENCY:ORDER_ID:SECRET_KEY)
        $expectedSign = md5($merchantId . ':' . $amount . ':' . $currency . ':' . $orderId . ':' . $secret);

        // 6. Сравниваем подписи
        if ($sign !== $expectedSign) {
            \Log::warning('[AAIO Callback] Неверная подпись', [
                'received_sign' => $sign,
                'expected_sign' => $expectedSign,
                'data' => [
                    'merchant_id' => $merchantId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'order_id' => $orderId,
                    'secret_used' => $secret // Только для логирования, НЕ показывать клиенту!
                ]
            ]);
            return response('Invalid signature', 400);
        }

        // 7. Ищем платеж в БД Wemx
        $payment = Payment::find($orderId);

        if (!$payment) {
            \Log::warning('[AAIO Callback] Платёж не найден в БД Wemx', ['order_id' => $orderId]);
            return response('Payment not found', 404);
        }

        // 8. Проверяем статус платежа
        if ($payment->status === 'completed') {
            \Log::info('[AAIO Callback] Платёж уже был обработан ранее', ['payment_id' => $orderId]);
            return response('OK'); // AAIO ожидает 200 OK
        }

        // 9. Обновляем статус платежа в Wemx
        try {
            $payment->completed(null, $request->all()); // Передаем данные callback'а
            \Log::info('[AAIO Callback] Платёж успешно завершён', ['payment_id' => $orderId]);
            return response('OK'); // ВАЖНО: AAIO ожидает именно "OK" в теле ответа
        } catch (\Exception $e) {
             \Log::error('[AAIO Callback] Ошибка при обновлении статуса платежа', [
                 'payment_id' => $orderId,
                 'error' => $e->getMessage()
             ]);
             return response('Internal Server Error', 500);
        }
    }
}