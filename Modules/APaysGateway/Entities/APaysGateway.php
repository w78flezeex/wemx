<?php

namespace Modules\APaysGateway\Entities;

use App\Models\Gateways\Gateway;
use App\Models\Gateways\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class APaysGateway implements PaymentGatewayInterface
{
    private static $rates = [
        'USD' => 100,
        'EUR' => 100,
        'UAH' => 0.5,
        'RUB' => 1,
    ];

    protected static function convertAmount(Payment $payment) {
        if (!isset(self::$rates[$payment->currency])) {
            throw new \Exception("Unknown currency");
        }

        return intval(($payment->amount * self::$rates[$payment->currency]) * 100);
    }

    protected static function getSignature($id, $param, $secretKey) {
        return md5($id . ':' . $param . ':' . $secretKey);
    }

    public static function processGateway(Gateway $gateway, Payment $payment)
    {
        $secretKey = $gateway->config()['secret_key'];
        $clientId = $gateway->config()['client_id'];

        $amount = self::convertAmount($payment);

        $response = Http::get("https://apays.io/backend/create_order", [
            'client_id' => $clientId,
            'order_id' => $payment->id,
            'amount' => $amount,
            'sign' => self::getSignature($payment->id, $amount, $secretKey),
        ]);

        return redirect()->intended($response->json()['url']);
    }

    public static function returnGateway(Request $request)
    {
        $gateway = Gateway::getGateway('APays');
        $secretKey = $gateway->config()['secret_key'];

        $data = $request->all();
        if (!isset($data['status'])) {
            return;
        }

        $status = $data['status'];
        if ($status != 'success') {
            return response()->json(['error' => "Payment status: {$status}"], 403);
        }

        $sign = $data['sign'];
        $paymentId = $data['order_id'];

        if (self::getSignature($paymentId, $status, $secretKey) == $sign ) {
            $payment = Payment::findOrFail($request->get('order_id'));
            if ($payment) {
                $payment->completed(null, $data);
            }
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
            'APays' => [
                'driver' => 'APays',
                'type' => 'once',
                'class' => 'Modules\APaysGateway\Entities\APaysGateway',
                'endpoint' => self::endpoint(),
                'refund_support' => false,
            ],
        ];
    }

    public static function endpoint(): string
    {
        return 'apays';
    }

    public static function getConfigMerge(): array
    {
        return [
            'client_id' => '',
            'secret_key' => '',
        ];
    }
}
