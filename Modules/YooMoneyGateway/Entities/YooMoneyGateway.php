<?php

namespace Modules\YooMoneyGateway\Entities;

use App\Models\Gateways\Gateway;
use App\Models\Gateways\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YooMoneyGateway implements PaymentGatewayInterface
{
    private static $rates = [
        'USD' => 100,
        'EUR' => 105,
        'UAH' => 0.5,
        'RUB' => 1,
    ];

    protected static function convertAmount(Payment $payment) {
        if (!isset(self::$rates[$payment->currency])) {
            throw new \Exception("Unknown currency");
        }

        return $payment->amount * self::$rates[$payment->currency];
    }

    public static function processGateway(Gateway $gateway, Payment $payment)
    {
        $wallet = $gateway->config()['wallet'];
        $amount = self::convertAmount($payment);

        return view('yoomoney::index', [
            'receiver' => $wallet,
            'amount' => $amount,
            'paymentId' => $payment->id,
        ]);
    }

    public static function returnGateway(Request $request)
    {

        $gateway = Gateway::getGateway('YooMoney');
        $notificationSecret = $gateway->config()['notification_secret'];

        $data = $request->all();
        Log::info("YooMoney callback", [$data]);
        if (!isset($data['label'])) {
            return response()->json(['error' => 'No label'], 403);;
        }

        $payment = Payment::findOrFail($data['label']);
        Log::info("YooMoney payment", [$payment]);
        $amount = self::convertAmount($payment);
        if ($data['withdraw_amount'] < $amount) {
            return response()->json(['error' => 'Amount is less than expected'], 403);
        }

        $expectedHash = sha1($data['notification_type'] . '&' .
            $data['operation_id'] . '&' .
            $data['amount'] . '&' .
            $data['currency'] . '&' .
            $data['datetime'] . '&' .
            $data['sender'] . '&' .
            $data['codepro'] . '&' .
            $notificationSecret . '&' .
            $data['label']
        );

        Log::info("YooMoney", ['expectedHash' => $expectedHash, 'actualHash' => $data['sha1_hash']]);
        if ($expectedHash == $data['sha1_hash']) {
            Log::info("YooMoney payment completed", [$payment]);
            $payment->completed(null, $data);
            return response()->json(['success' => 'Payment completed successfully']);
        } else {
            return response()->json(['error' => 'Bad signature'], 403);
        }
    }


    public static function processRefund(Payment $payment, array $data)
    {
        // TODO: Implement processRefund() method.
    }

    public static function checkSubscription(Gateway $gateway, $subscriptionId): bool
    {
        // TODO: Implement checkSubscription() method.
    }

    public static function drivers(): array
    {
        return [
            'YooMoney' => [
                'driver' => 'YooMoney',
                'type' => 'once',
                'class' => 'Modules\YooMoneyGateway\Entities\YooMoneyGateway',
                'endpoint' => self::endpoint(),
                'refund_support' => false,
            ],
        ];
    }

    public static function endpoint(): string
    {
        return 'yoomoney';
    }

    public static function getConfigMerge(): array
    {
        return [
            'wallet' => '',
            'notification_secret' => '',
        ];
    }
}
