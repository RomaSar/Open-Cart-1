<?php
// catalog/controller/extension/module/moysklad_webhook.php
class ControllerExtensionModuleMoyskladWebhook extends Controller {
    // Приходит webhook из МоегоСклада — примерный формат зависит от настроек в МойСклад
    public function index() {
        // Проверка простого токена (опционально)
        $secret = $this->config->get('module_moysklad_token'); // в простом варианте используем тот же токен
        $headers = getallheaders();
        // Пример: X-API-KEY или Authorization
        if (isset($headers['Authorization'])) {
            // можно проверить Bearer ...
        }

        $payload = file_get_contents('php://input');
        $this->log->write('MoySklad webhook payload: ' . $payload);

        // Здесь распарсим уведомление и обновим статус заказа/остатки и т.д.
        // Для примера возвращаем 200
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['status' => 'ok']));
    }
}
