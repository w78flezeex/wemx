<?php

namespace Modules\AAIOGateway\Entities;

use App\Models\Gateways\Gateway;
use App\Models\Gateways\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AAIOGateway implements PaymentGatewayInterface
{
    /**
     * Генерирует подпись для нового API AAIO (SHA256).
     * Алгоритм: SHA256(MERCHANT_ID:AMOUNT:CURRENCY:SECRET_KEY_1:ORDER_ID)
     *
     * @param string $merchantId ID магазина
     * @param float|string $amount Сумма
     * @param string $currency Валюта
     * @param string $secretKey1 Первый секретный ключ (для создания платежа)
     * @param string $orderId ID заказа
     * @return string
     */
    protected static function generateSignature($merchantId, $amount, $currency, $secretKey1, $orderId)
    {
        $signString = implode(':', [$merchantId, $amount, $currency, $secretKey1, $orderId]);
        \Log::debug('[AAIO] Строка для подписи (новое API): ' . $signString);
        return hash('sha256', $signString);
    }

    /**
     * Создаёт платёж через новое API AAIO.
     *
     * @param Gateway $gateway
     * @param Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public static function processGateway(Gateway $gateway, Payment $payment)
    {
        $config = $gateway->config();
        $merchantId = $config['shop_id'] ?? null;
        $secretKey1 = $config['api_key'] ?? null; // Первый ключ для создания платежа
        $currency = 'RUB';

        if (!$merchantId || !$secretKey1) {
            \Log::error('[AAIO] Не настроены shop_id или api_key', ['gateway_id' => $gateway->id]);
            throw new \Exception("AAIO не настроен: отсутствуют shop_id или api_key");
        }

        $amount = $payment->amount;
        $orderId = $payment->id;
        $desc = 'Пополнение баланса #' . $orderId;

        $sign = self::generateSignature($merchantId, $amount, $currency, $secretKey1, $orderId);

        try {
            $response = Http::asForm()->acceptJson()->post('https://aaio.so/merchant/get_pay_url', [
                'merchant_id' => $merchantId,
                'amount' => $amount,
                'currency' => $currency,
                'order_id' => $orderId,
                'sign' => $sign,
                'desc' => $desc,
                'lang' => 'ru',
            ]);

            \Log::info('[AAIO] Ответ от API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                \Log::error('[AAIO] Ошибка API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Ошибка при создании платежа в AAIO: HTTP ' . $response->status());
            }

            $data = $response->json();

            if (!isset($data['type']) || $data['type'] !== 'success') {
                \Log::error('[AAIO] Ошибка API (логика)', ['response' => $data]);
                throw new \Exception('Ошибка AAIO: ' . ($data['message'] ?? 'Неизвестная ошибка'));
            }

            $payUrl = $data['url'];

            \Log::info('[AAIO] Платёж успешно создан', [
                'payment_id' => $orderId,
                'aaio_url' => $payUrl
            ]);

            return redirect($payUrl);

        } catch (\Exception $e) {
            \Log::error('[AAIO] Исключение при создании платежа', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Не удалось создать платёж в AAIO: ' . $e->getMessage());
        }
    }

    /**
     * Обрабатывается при возврате пользователя с AAIO.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function returnGateway(Request $request)
    {
        \Log::info('[AAIO] Пользователь вернулся с AAIO', ['query_params' => $request->query()]);
        return redirect()->route('home');
    }

    // --- Стабы для интерфейса ---

    public static function processRefund(Payment $payment, array $data)
    {
        \Log::warning('[AAIO] Метод processRefund не реализован');
        return false;
    }

    public static function checkSubscription(Gateway $gateway, $subscriptionId): bool
    {
        \Log::warning('[AAIO] Метод checkSubscription не реализован');
        return false;
    }

    // --- Регистрация драйвера ---

    public static function drivers(): array
    {
        return [
            'AAIO' => [
                'driver' => 'AAIO',
                'type' => 'once',
                'class' => 'Modules\AAIOGateway\Entities\AAIOGateway',
                'endpoint' => self::endpoint(),
                'refund_support' => false,
            ],
        ];
    }

    public static function endpoint(): string
    {
        return 'aaio';
    }

    public static function getConfigMerge(): array
    {
        return [
            'api_key' => '',  // <-- Первый секретный ключ (для создания платежа)
            'shop_id' => '',  // <-- ID магазина (UUID)
            'secret' => '',   // <-- Второй секретный ключ (для callback)
        ];
    }
}