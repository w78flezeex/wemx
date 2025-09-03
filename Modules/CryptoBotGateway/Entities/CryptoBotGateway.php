<?php

namespace Modules\CryptoBotGateway\Entities;

use App\Models\Gateways\Gateway;
use App\Models\Gateways\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoBotGateway implements PaymentGatewayInterface
{
    /**
     * Создаёт счёт в @CryptoBot.
     *
     * @param Gateway $gateway
     * @param Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public static function processGateway(Gateway $gateway, Payment $payment)
    {
        $config = $gateway->config();
        $apiToken = $config['api_token'] ?? null;

        if (!$apiToken) {
            \Log::error('[CryptoBot] Не настроен API токен', ['gateway_id' => $gateway->id]);
            throw new \Exception("CryptoBot не настроен: отсутствует API токен");
        }

        // --- НАСТРОЙКА ДЛЯ ОПЛАТЫ В ФИАТНОЙ ВАЛЮТЕ ПО КУРСУ ---
        $currencyType = 'fiat';              // Тип валюты - фиат
        $fiatCurrency = $payment->currency ?? 'USD'; // Фиатная валюта из платежа
        // Если валюта платежа не поддерживается, используем USD по умолчанию
        $supportedFiats = ['USD', 'EUR', 'RUB', 'BYN', 'UAH', 'GBP', 'CNY', 'KZT', 'UZS', 'GEL', 'TRY', 'AMD', 'THB', 'INR', 'BRL', 'IDR', 'AZN', 'AED', 'PLN', 'ILS'];
        if (!in_array($fiatCurrency, $supportedFiats)) {
            $fiatCurrency = 'USD';
            \Log::warning("[CryptoBot] Валюта '{$payment->currency}' не поддерживается, используется USD.", ['payment_id' => $payment->id]);
        }
        $amountInFiat = $payment->amount;    // Сумма в фиатной валюте
        // -------------------------------------------------------

        $orderId = $payment->id;
        $description = 'Пополнение баланса #' . $orderId;

        try {
            $requestData = [
                'currency_type' => $currencyType,
                'fiat' => $fiatCurrency,
                'amount' => $amountInFiat,
                'description' => $description,
                'order_id' => $orderId,
                'payload' => json_encode(['order_id' => $orderId, 'payment_id' => $payment->id]),
                'paid_btn_name' => 'viewItem',
                'paid_btn_url' => url('/'), // Или другой URL
                'allow_comments' => false,
                'allow_anonymous' => false,
            ];

            // Удаляем пустые поля
            $requestData = array_filter($requestData, function($value) {
                return $value !== null && $value !== '';
            });

            $response = Http::withHeaders([
                'Crypto-Pay-API-Token' => $apiToken,
            ])->asForm()->post('https://pay.crypt.bot/api/createInvoice', $requestData);

            \Log::info('[CryptoBot] Ответ от API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                \Log::error('[CryptoBot] Ошибка API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Ошибка при создании счёта в CryptoBot: HTTP ' . $response->status());
            }

            $data = $response->json();

            if (!isset($data['ok']) || !$data['ok']) {
                \Log::error('[CryptoBot] Ошибка API (логика)', ['response' => $data]);
                throw new \Exception('Ошибка CryptoBot: ' . ($data['error']['message'] ?? 'Неизвестная ошибка'));
            }

            $invoice = $data['result'];
            $payUrl = $invoice['bot_invoice_url']; // Используем актуальное поле

            \Log::info('[CryptoBot] Счёт успешно создан', [
                'payment_id' => $orderId,
                'invoice_id' => $invoice['invoice_id'],
                'pay_url' => $payUrl
            ]);

            // Сохраняем invoice_id для возможной отмены
            $payment->update(['transaction_id' => $invoice['invoice_id']]);

            return redirect($payUrl);

        } catch (\Exception $e) {
            \Log::error('[CryptoBot] Исключение при создании счёта', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Не удалось создать счёт в CryptoBot: ' . $e->getMessage());
        }
    }

    /**
     * Обрабатывается при возврате пользователя с CryptoBot.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function returnGateway(Request $request)
    {
        \Log::info('[CryptoBot] Пользователь вернулся с CryptoBot', ['query_params' => $request->query()]);
        // Перенаправляем на главную или в личный кабинет
        // Попробуем route('home'), если не существует - используем url('/')
        try {
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect(url('/'));
        }
    }

    // --- Реализация интерфейса ---

    public static function processRefund(Payment $payment, array $data)
    {
        // TODO: Реализовать возврат, если API поддерживает
        \Log::warning('[CryptoBot] Метод processRefund не реализован');
        return false;
    }

    public static function checkSubscription(Gateway $gateway, $subscriptionId): bool
    {
        // TODO: Реализовать, если API поддерживает подписки
        \Log::warning('[CryptoBot] Метод checkSubscription не реализован');
        return false;
    }

    // --- Регистрация драйвера ---

    public static function drivers(): array
    {
        return [
            'CryptoBot' => [
                'driver' => 'CryptoBot',
                'type' => 'once',
                'class' => 'Modules\CryptoBotGateway\Entities\CryptoBotGateway',
                'endpoint' => self::endpoint(),
                'refund_support' => false,
            ],
        ];
    }

    public static function endpoint(): string
    {
        return 'cryptobot';
    }

    /**
     * Поля конфигурации для админки Wemx.
     *
     * @return array
     */
    public static function getConfigMerge(): array
    {
        return [
            'api_token' => '', // <-- API токен от @CryptoBot
        ];
    }
}