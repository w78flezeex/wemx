# 🚀 Новые модули для WemX

В этой папке созданы три инновационных модуля для расширения функциональности WemX:

## 📋 Список модулей

### 1. 🏆 AchievementSystem
**Система достижений и геймификации**
- Система достижений с категориями
- Отслеживание прогресса пользователей
- Автоматическая проверка достижений
- Система очков за достижения
- Интеграция с существующими событиями WemX

### 2. 🧠 CustomRecommendations  
**Система персонализированных рекомендаций**
- Алгоритмы машинного обучения
- Персонализация на основе поведения
- Анализ предпочтений пользователей
- Умные рекомендации продуктов
- Автоматическое обновление предпочтений

### 3. 🤖 AIChatbot
**Искусственный интеллект чат-бот**
- ИИ чат-бот с естественным языком
- Управление чат-сессиями
- Контекстное понимание разговора
- Анализ сообщений и метаданных
- Интеграция с внешними AI сервисами

## 🛠️ Установка

### Общие требования
- PHP 8.0+
- Laravel 11+
- WemX 2.2.1+

### Пошаговая установка

1. **Скопируйте модули** в папку `Modules/`

2. **Установите зависимости** для каждого модуля:
```bash
cd Modules/AchievementSystem && composer install
cd Modules/CustomRecommendations && composer install  
cd Modules/AIChatbot && composer install
```

3. **Запустите миграции** для всех модулей:
```bash
php artisan module:migrate AchievementSystem
php artisan module:migrate CustomRecommendations
php artisan module:migrate AIChatbot
```

4. **Опубликуйте конфигурации**:
```bash
php artisan module:publish AchievementSystem
php artisan module:publish CustomRecommendations
php artisan module:publish AIChatbot
```

5. **Перезапустите сервер**:
```bash
php artisan optimize:clear
```

## 🔧 Конфигурация

### AchievementSystem
```env
ACHIEVEMENT_SYSTEM_ENABLED=true
ACHIEVEMENT_POINTS_MULTIPLIER=1
ACHIEVEMENT_AUTO_CHECK=true
ACHIEVEMENT_NOTIFICATIONS=true
```

### CustomRecommendations
```env
RECOMMENDATION_SYSTEM_ENABLED=true
RECOMMENDATION_ENGINE=default
RECOMMENDATION_CACHE_TTL=3600
```

### AIChatbot
```env
AI_CHATBOT_ENABLED=true
AI_SERVICE_PROVIDER=openai
OPENAI_API_KEY=your_api_key_here
AI_CHATBOT_MODEL=gpt-3.5-turbo
```

## 📚 Использование

### AchievementSystem
```php
// Проверка достижения
checkAchievement($userId, 'first_order');

// Получение достижений пользователя
$achievements = getUserAchievements($userId);

// Получение очков пользователя
$points = getUserAchievementPoints($userId);
```

### CustomRecommendations
```php
// Получение рекомендаций для пользователя
$recommendations = app('recommendation.engine')->getRecommendations($userId);

// Обновление предпочтений
app('recommendation.engine')->updatePreferences($userId, $data);

// Анализ поведения
app('recommendation.engine')->analyzeBehavior($userId);
```

### AIChatbot
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

## 🎯 Возможности интеграции

Эти модули интегрируются с существующей системой WemX:

- **События**: Автоматически реагируют на создание заказов, платежи, регистрацию
- **Пользователи**: Расширяют функциональность пользовательских профилей
- **Админ-панель**: Предоставляют интерфейсы для управления
- **API**: Доступны через REST API для внешних интеграций

## 🔮 Будущие улучшения

- **Мобильные приложения**: API для мобильных клиентов
- **Аналитика**: Расширенные отчеты и дашборды
- **Интеграции**: Подключение к внешним сервисам
- **Многоязычность**: Поддержка различных языков
- **Темы**: Кастомизируемые интерфейсы

## 📞 Поддержка

Для получения поддержки по модулям:
- Создайте issue в репозитории
- Обратитесь к команде WemX
- Проверьте документацию модулей

---

**Создано для WemX** 🚀
*Расширяйте возможности вашей системы биллинга!*
