# Подробное описание процесса автоматической оплаты и начисления после платежа в WemX

## Общая архитектура системы

WemX - это система биллинга для игровых серверов. Система поддерживает автоматическую обработку платежей, создание и управление заказами, а также интеграцию с различными игровыми панелями (Pterodactyl, Wisp, Hestia).

**Важно:** Проект находится в директории `/var/www/billing`.

## 1. Структура основных компонентов

### 1.1 Основные модели
- **Payment** - платежи и инвойсы
- **Order** - заказы и сервисы
- **Package** - тарифы и пакеты
- **User** - пользователи системы
- **Gateway** - платежные шлюзы

### 1.2 Обработчики событий
- **NewOrder** - создание нового заказа
- **Renewal** - продление существующего заказа
- **Upgrade** - апгрейд заказа
- **Cancel** - отмена заказа

### 1.3 Фоновые задачи (Cronjobs)
- **SuspendExpiredOrders** - приостановка просроченных заказов
- **BalanceAutoRenew** - автоматическое продление с баланса
- **ExpiryReminder** - напоминания об истечении срока
- **DeleteTerminateOrders** - удаление завершенных заказов

## 2. Процесс автоматической оплаты

### 2.1 Создание платежа

```php
// Создание нового платежа
$payment = Payment::create([
    'user_id' => $user->id,
    'order_id' => $order->id,
    'amount' => $amount,
    'currency' => 'USD',
    'status' => 'unpaid',
    'gateway' => $gatewayConfig,
    'handler' => $handlerClass,
    'due_date' => now()->addDays(7)
]);
```

### 2.2 Обработка платежа через шлюз

```php
// В модели Payment::completed()
public function completed($transaction_id = null, $data = [])
{
    // Вызов обработчика события
    if (isset($this->handler)) {
        $handler = new $this->handler;
        $handler->onPaymentCompleted($this);
    }
    
    // Обновление статуса платежа
    $this->status = 'paid';
    $this->transaction_id = $transaction_id;
    $this->data = $data;
    $this->save();
    
    // Обработка партнерской программы
    if (request()->hasCookie('affiliate_invite')) {
        // Начисление комиссии партнеру
    }
    
    // Создание PDF инвойса
    $invoice = Pdf::loadView('invoice-pdf', ['payment' => $this]);
    
    // Отправка email уведомления
    $this->user->email([
        'subject' => 'Payment Completed',
        'content' => 'Your payment has been processed successfully',
        'attachment' => $invoice
    ]);
    
    // Создание уведомления
    $this->user->notify([
        'type' => 'success',
        'message' => 'Payment completed successfully'
    ]);
    
    // Диспатч события
    Events\PaymentCompleted::dispatch($this);
}
```

### 2.3 Обработчики событий платежей

#### NewOrder Handler
```php
class NewOrder extends ServiceHandler
{
    public function onPaymentCompleted(Payment $payment)
    {
        // Создание заказа на основе платежа
        $order = Order::createOrder($payment);
        
        // Создание сервиса в панели
        $order->create();
    }
}
```

#### Renewal Handler
```php
class Renewal extends ServiceHandler
{
    public function onPaymentCompleted(Payment $payment)
    {
        // Продление заказа на указанный период
        $payment->order->extend($payment->options['period']);
    }
    
    public function onPaymentExpired(Payment $payment)
    {
        // Приостановка заказа при истечении платежа
        $payment->order->suspend();
    }
}
```

## 3. Процесс начисления после платежа

### 3.1 Создание заказа

```php
// В модели Order::createOrder()
public static function createOrder(Payment $payment)
{
    $order = new Order([
        'user_id' => $payment->user_id,
        'package_id' => $payment->package_id,
        'name' => $payment->package->name,
        'service' => $payment->package->service,
        'data' => $payment->package->data,
        'options' => $payment->options,
        'period' => $payment->price->period,
        'price' => $payment->price,
        'due_date' => now()->addDays($payment->price->period),
        'status' => 'pending'
    ]);
    
    $order->save();
    return $order;
}
```

### 3.2 Активация сервиса

```php
// В модели Order::create()
public function create()
{
    try {
        // Создание сервиса в панели (Pterodactyl, Wisp, etc.)
        $this->service()->create();
        
        // Диспатч события активации
        Events\Order\OrderActivated::dispatch($this);
        
    } catch (\Exception $error) {
        // Логирование ошибки
        ErrorLog::create([
            'user_id' => $this->user->id,
            'order_id' => $this->id,
            'source' => 'server::create',
            'severity' => 'CRITICAL',
            'message' => $error->getMessage(),
        ]);
    }
}
```

### 3.3 Интеграция с игровыми панелями

```php
// Пример для Pterodactyl
class PterodactylService extends ServiceInterface
{
    public function create()
    {
        $response = $this->api->createServer([
            'name' => $this->order->name,
            'egg' => $this->order->option('egg_id'),
            'memory' => $this->order->option('memory_limit'),
            'disk' => $this->order->option('disk_limit'),
            'cpu' => $this->order->option('cpu_limit'),
            'swap' => 0,
            'io' => 500,
            'feature_limits' => [
                'databases' => $this->order->option('database_limit'),
                'allocations' => $this->order->option('allocation_limit'),
                'backups' => $this->order->option('backup_limit')
            ]
        ]);
        
        $this->order->setExternalId($response['id']);
    }
}
```

## 4. Автоматическое продление

### 4.1 Система напоминаний

```php
// Cronjob: ExpiryReminder
class ExpiryReminder extends Command
{
    public function handle()
    {
        // Заказы, которые истекают через 5 дней
        $orders = Order::where('due_date', '<=', now()->addDays(5))
                      ->where('status', 'active')
                      ->get();
        
        foreach ($orders as $order) {
            // Отправка email напоминания
            $order->user->email([
                'subject' => 'Order Expiry Reminder',
                'content' => "Your order {$order->name} expires on {$order->due_date}"
            ]);
        }
    }
}
```

### 4.2 Автоматическое продление с баланса

```php
// Cronjob: BalanceAutoRenew
class BalanceAutoRenew extends Command
{
    public function handle()
    {
        // Заказы с включенным автопродлением
        $orders = Order::where('due_date', '<=', now()->addDays(5))
                      ->where('status', 'active')
                      ->where('auto_balance_renew', true)
                      ->get();
        
        foreach ($orders as $order) {
            $user = $order->user;
            
            // Проверка достаточности баланса
            if ($user->balance >= $order->price()->renewal_price) {
                // Списание с баланса
                $user->balance(
                    "Automatic renewal of order {$order->name}",
                    '-',
                    $order->price()->renewal_price
                );
                
                // Продление заказа
                $order->extend($order->price()->period);
                
                // Уведомление о продлении
                $this->orderRenewed($order);
            } else {
                // Уведомление о недостатке средств
                $this->notEnoughBalance($order);
            }
        }
    }
}
```

### 4.3 Приостановка просроченных заказов

```php
// Cronjob: SuspendExpiredOrders
class SuspendExpiredOrders extends Command
{
    public function handle()
    {
        $expired_orders = Order::getExpiredOrders();
        
        foreach ($expired_orders as $order) {
            try {
                // Отправка email о приостановке
                $order->user->email([
                    'subject' => 'Order Suspended',
                    'content' => "Your order {$order->name} has been suspended due to non-payment"
                ]);
                
                // Приостановка сервиса
                $order->suspend();
                
            } catch (\Exception $error) {
                // Принудительная приостановка при ошибке
                $order->forceSuspend();
                ErrorLog::create([
                    'source' => 'cron:orders:suspend-expired',
                    'severity' => 'CRITICAL',
                    'message' => "Failed to suspend order {$order->id}: {$error->getMessage()}"
                ]);
            }
        }
    }
}
```

## 5. Система событий и уведомлений

### 5.1 События платежей

```php
// PaymentCompleted Event
class PaymentCompleted
{
    public function __construct(public Payment $payment) {}
}

// PaymentRefunded Event
class PaymentRefunded
{
    public function __construct(public Payment $payment, public float $refunded_amount) {}
}
```

### 5.2 События заказов

```php
// OrderActivated Event
class OrderActivated
{
    public function __construct(public Order $order) {}
}

// OrderRenewed Event
class OrderRenewed
{
    public function __construct(public Order $order) {}
}

// OrderSuspended Event
class OrderSuspended
{
    public function __construct(public Order $order) {}
}
```

### 5.3 Обработка событий

```php
// В модели Order
public function fireEvent(string $event): void
{
    OrderEvent::handle($this, $event);
}

// Пример использования
public function extend($days = 0): void
{
    $this->due_date = $this->due_date->addDays($days);
    $this->last_renewed_at = Carbon::now();
    $this->save();
    
    // Вызов события продления
    $this->fireEvent('renewal');
    
    // Диспатч события
    Events\Order\OrderRenewed::dispatch($this);
}
```

## 6. Интеграция с платежными шлюзами

### 6.1 Поддерживаемые шлюзы

- **PayPal** - PayPal Checkout и Subscriptions
- **Stripe** - Stripe Checkout и Subscriptions
- **Paddle** - Paddle Checkout
- **Balance** - Внутренний баланс
- **Bitpave** - Криптоплатежи
- **Tebex** - Tebex интеграция

### 6.2 Конфигурация шлюза

```php
// В модели Gateway
public function config(): array
{
    return [
        'api_key' => 'your_api_key',
        'webhook_secret' => 'your_webhook_secret',
        'sandbox' => true,
        'currency' => 'USD'
    ];
}
```

### 6.3 Обработка webhook'ов

```php
// Пример для Stripe
public function handleWebhook(Request $request)
{
    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');
    
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $this->config['webhook_secret']
        );
        
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handleSubscriptionRenewal($event->data->object);
                break;
        }
        
    } catch (\Exception $e) {
        return response('Webhook Error', 400);
    }
}
```

## 7. Система уведомлений

### 7.1 Email уведомления

```php
// В модели User
public function email($data)
{
    EmailMessage::create([
        'user_id' => $this->id,
        'subject' => $data['subject'],
        'content' => $data['content'],
        'button' => $data['button'] ?? null,
        'attachment' => $data['attachment'] ?? null
    ]);
}
```

### 7.2 Внутренние уведомления

```php
// В модели User
public function notify($data)
{
    Notification::create([
        'user_id' => $this->id,
        'type' => $data['type'],
        'icon' => $data['icon'],
        'message' => $data['message'],
        'button_url' => $data['button_url'] ?? null
    ]);
}
```

## 8. Обработка ошибок и логирование

### 8.1 Система логирования

```php
// ErrorLog модель
class ErrorLog extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'source',
        'severity',
        'message'
    ];
}

// Использование
ErrorLog::create([
    'source' => 'payment::gateway',
    'severity' => 'ERROR',
    'message' => 'Payment gateway error: ' . $error->getMessage()
]);
```

### 8.2 Обработка исключений

```php
// В обработчиках сервисов
try {
    $this->service()->create();
} catch (\Exception $error) {
    ErrorLog::create([
        'user_id' => $this->user->id,
        'order_id' => $this->id,
        'source' => 'server::create',
        'severity' => 'CRITICAL',
        'message' => $error->getMessage(),
    ]);
    
    // Отправка уведомления администратору
    // Дополнительная обработка ошибки
}
```

## 9. Настройка cron задач

### 9.1 Расписание выполнения

```bash
# /etc/crontab
# Проверка просроченных заказов каждые 5 минут
*/5 * * * * cd /var/www/billing && php artisan cron:orders:suspend-expired

# Автоматическое продление каждый час
0 * * * * cd /var/www/billing && php artisan cron:orders:balance-auto-renew

# Напоминания об истечении срока каждый день в 9:00
0 9 * * * cd /var/www/billing && php artisan cron:orders:expiry-reminder

# Удаление завершенных заказов каждый день в 2:00
0 2 * * * cd /var/www/billing && php artisan cron:orders:delete-terminate
```

### 9.2 Мониторинг выполнения

```php
// В командах добавлено логирование
$this->info('Loaded a list of expired orders ' . $expired_orders->count());
$progressBar = $this->output->createProgressBar(count($expired_orders));
$progressBar->start();

foreach ($expired_orders as $order) {
    // Обработка заказа
    $progressBar->advance();
}

$progressBar->finish();
$this->info('Task Completed: all expired orders were suspended');
```

## 10. Безопасность и валидация

### 10.1 Валидация платежей

```php
// Проверка подписи webhook'а
public function verifyWebhookSignature($payload, $signature)
{
    $expected_signature = hash_hmac('sha256', $payload, $this->config['webhook_secret']);
    return hash_equals($expected_signature, $signature);
}
```

### 10.2 Защита от дублирования

```php
// В модели Payment
public function completed($transaction_id = null, $data = [])
{
    // Проверка на повторную обработку
    if ($this->type == 'once' and $this->status == 'paid') {
        return 0;
    }
    
    // Обработка платежа
    // ...
}
```

## 11. Решение проблем с платежными шлюзами

### 11.1 Проблема YooMoney "Перевести не получится"

Если YooMoney показывает ошибку "Перевести не получится", выполните следующие шаги:

1. **Проверьте номер кошелька** - должен быть в формате `41001XXXXXXXXX` (15 цифр)
2. **Обновите шаблон формы** - используйте современный API YooMoney
3. **Проверьте настройки** - убедитесь, что notification_secret совпадает с настройками в YooMoney
4. **Проверьте SSL** - YooMoney требует HTTPS для webhook'ов

Подробное решение смотрите в файле `yoomoney_fix.md`.

### 11.2 Общие проблемы платежных шлюзов

- **Неверная подпись webhook'а** - проверьте секретные ключи
- **Проблемы с кодировкой** - убедитесь в использовании UTF-8
- **Блокировка по IP** - проверьте IP сервера в настройках шлюза
- **Истекшие сертификаты** - обновите SSL сертификаты

## Заключение

Система автоматической оплаты и начисления в WemX представляет собой комплексное решение, которое включает:

1. **Модульную архитектуру** с разделением ответственности между компонентами
2. **Гибкую систему обработчиков** для различных типов платежей
3. **Автоматическое управление жизненным циклом** заказов
4. **Интеграцию с популярными платежными шлюзами**
5. **Систему уведомлений** для пользователей и администраторов
6. **Надежное логирование** и обработку ошибок
7. **Автоматическое продление** с различных источников
8. **Безопасную обработку** webhook'ов и платежей

Система обеспечивает полную автоматизацию процесса от создания платежа до активации сервиса, включая все промежуточные этапы управления заказами.
