<?php

namespace Modules\GatewayPack\Gateways\Once;

use App\Models\Gateways\Gateway;
use App\Models\Gateways\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Modules\GatewayPack\Traits\HelperGateway;

class YooMoney implements PaymentGatewayInterface
{
    use HelperGateway;

    public static string $quickPayUrl = 'https://yoomoney.ru/quickpay/confirm';

    public static string $endpoint = 'yoomoney';

    public static string $type = 'once';

    public static bool $refund_support = false;

    public static function getConfigMerge(): array
    {
        return [
            'reciever' => '4100117143086353',
            'secret_key' => '',
        ];
    }

    public static function processGateway(Gateway $gateway, Payment $payment)
    {
        $amount = $payment->amount;

        $response = Http::asForm()->post('https://yoomoney.ru/quickpay/confirm', [
            'receiver' => $gateway->config['reciever'],
            'label' => $payment->id,
            'quickpay-form' => 'button',
            'sum' => $amount,
        ]);

        if ($response->successful()) {
            $redirectUrl = $response->header('Location');

            self::log($redirectUrl, 'info');

            return redirect($redirectUrl);
        }
    }

    public static function returnGateway(Request $request)
    {

    }
}
