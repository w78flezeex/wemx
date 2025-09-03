# CustomRecommendations - Система персонализированных рекомендаций для WemX

## Описание
Модуль системы персонализированных рекомендаций для WemX, использующий машинное обучение для анализа поведения пользователей и предоставления релевантных предложений.

## Возможности
- 🧠 Алгоритмы машинного обучения
- 👤 Персонализация на основе поведения
- 📊 Анализ предпочтений пользователей
- 🎯 Умные рекомендации продуктов
- 🔄 Автоматическое обновление предпочтений

## Установка
1. Скопируйте модуль в папку `Modules/`
2. Запустите миграции: `php artisan module:migrate CustomRecommendations`
3. Опубликуйте конфигурацию: `php artisan module:publish CustomRecommendations`

## Использование
```php
// Получение рекомендаций для пользователя
$recommendations = app('recommendation.engine')->getRecommendations($userId);

// Обновление предпочтений
app('recommendation.engine')->updatePreferences($userId, $data);

// Анализ поведения
app('recommendation.engine')->analyzeBehavior($userId);
```

## Структура
- `Entities/` - Модели данных
- `Services/` - Сервисы рекомендаций
- `Database/Migrations/` - Миграции базы данных
- `Providers/` - Сервис-провайдеры
- `helpers.php` - Вспомогательные функции

## Требования
- PHP 8.0+
- Laravel 11+
- WemX 2.2.1+
- Guzzle HTTP Client
