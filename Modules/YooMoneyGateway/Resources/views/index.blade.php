<body onload="document.forms[0].submit()">
<form method="POST" action="https://yoomoney.ru/quickpay/confirm">
    <input type="hidden" name="receiver" value="{{ $receiver }}"/>
    <input type="hidden" name="label" value="{{ $paymentId  }}"/>
    <input type="hidden" name="quickpay-form" value="button"/>
    <input type="hidden" name="sum" value="{{ $amount }}" data-type="number"/>
    <input type="hidden" name="paymentType" value="PC">
</form>
</body>
