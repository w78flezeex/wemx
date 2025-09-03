@extends('admin.default.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Настройки @CryptoBot</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.settings.update', 'cryptobot') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>API токен</label>
                <input type="text" name="api_token" class="form-control" value="{{ settings('cryptobot.api_token') }}" required>
                <small class="form-text text-muted">Получите токен в боте @CryptoBot -> Crypto Pay -> My Apps -> Выберите ваше приложение -> API Token</small>
            </div>
            <div class="form-group">
                <label>Валюта по умолчанию (если currency_type=crypto)</label>
                <select name="default_asset" class="form-control">
                    <option value="USDT" {{ settings('cryptobot.default_asset') === 'USDT' ? 'selected' : '' }}>USDT</option>
                    <option value="TON" {{ settings('cryptobot.default_asset') === 'TON' ? 'selected' : '' }}>TON</option>
                    <option value="BTC" {{ settings('cryptobot.default_asset') === 'BTC' ? 'selected' : '' }}>BTC</option>
                    <option value="ETH" {{ settings('cryptobot.default_asset') === 'ETH' ? 'selected' : '' }}>ETH</option>
                    <option value="LTC" {{ settings('cryptobot.default_asset') === 'LTC' ? 'selected' : '' }}>LTC</option>
                    <option value="BNB" {{ settings('cryptobot.default_asset') === 'BNB' ? 'selected' : '' }}>BNB</option>
                    <option value="TRX" {{ settings('cryptobot.default_asset') === 'TRX' ? 'selected' : '' }}>TRX</option>
                    <option value="USDC" {{ settings('cryptobot.default_asset') === 'USDC' ? 'selected' : '' }}>USDC</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>
@endsection
