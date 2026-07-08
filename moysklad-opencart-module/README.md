# MoySklad Integration for OpenCart 4.1.0.3

Краткое описание
----------------
Этот пакет — базовый модуль интеграции с API "МойСклад". Предоставляет:
- Админ-форму для ввода API URL и токена.
- Ручные кнопки синхронизации товаров и заказов.
- Пример webhook endpoint в каталоге.
- Простой обработчик события добавления заказа, который отправляет заказ в МойСклад.

Установка
---------
1. Скопируйте файлы в соответствующие папки вашего OpenCart или используйте установщик OpenCart:
   - admin/controller/extension/module/moysklad.php
   - admin/model/extension/module/moysklad.php
   - admin/language/ru-ru/extension/module/moysklad.php
   - admin/view/template/extension/module/moysklad.twig
   - catalog/controller/extension/module/moysklad_webhook.php
   - catalog/model/extension/module/moysklad.php
   - catalog/language/ru-ru/extension/module/moysklad.php

2. В админке: Extensions -> Extensions -> Выберите Type = Modules (или Extensions -> Extensions -> Modules),
   найдите "МойСклад — интеграция" и установите.

3. Перейдите в настройки модуля и укажите:
   - API URL: https://online.moysklad.ru
   - Токен доступа: ваш Bearer токен (или OAuth токен)

4. Отправьте тестовую синхронизацию товаров/заказов через кнопку в интерфейсе модуля.

Примечания и дальнейшая доработка
--------------------------------
- Сейчас в коде используется упрощённый метод передачи токена (Bearer). Если вы используете OAuth2 с refresh token — нужно реализовать flow обновления токена.
- Конечные эндпойнты API и модели сущностей (product/customerorder и т.п.) в МоемСкладе могут отличаться в зависимости от версии API — уточните у документации МоегоСклада и корректируйте пути/поля.
- Для продакшен-решения рекомендую:
  - Реализовать обработку SKU <-> UUID номенклатуры.
  - Отправку позиций заказа (products) с информацией о количестве/цене.
  - Обработку ошибок и повторные попытки.
  - Безопасную проверку webhook (подпись или секрет).

Варианты упаковки
-----------------
Вы можете скачать ZIP всего репозитория через GitHub: нажмите Code -> Download ZIP на странице репозитория.
