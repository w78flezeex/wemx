# AIChatbot - Искусственный интеллект чат-бот для WemX

## Описание
Модуль искусственного интеллекта чат-бота для WemX, предоставляющий автоматизированную поддержку клиентов с использованием современных AI технологий.

## Возможности
- 🤖 ИИ чат-бот с естественным языком
- 💬 Управление чат-сессиями
- 🧠 Контекстное понимание разговора
- 📊 Анализ сообщений и метаданных
- 🔄 Интеграция с внешними AI сервисами

## Установка
1. Скопируйте модуль в папку `Modules/`
2. Запустите миграции: `php artisan module:migrate AIChatbot`
3. Опубликуйте конфигурацию: `php artisan module:publish AIChatbot`
4. Настройте API ключи для AI сервисов

## Использование
```php
// Создание новой чат-сессии
$session = ChatSession::create([
    'user_id' => $userId,
    'session_id' => Str::uuid()
]);

// Добавление сообщения
$message = $session->addMessage('Привет!', 'user');

// Получение AI ответа
$aiResponse = app('ai.service')->generateResponse($session);
```

## Структура
- `Entities/` - Модели данных
- `Services/` - AI сервисы
- `Database/Migrations/` - Миграции базы данных
- `Providers/` - Сервис-провайдеры
- `helpers.php` - Вспомогательные функции

## Требования
- PHP 8.0+
- Laravel 11+
- WemX 2.2.1+
- Guzzle HTTP Client
- API ключи для AI сервисов (OpenAI, Claude, etc.)
