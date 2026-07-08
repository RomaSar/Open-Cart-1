<?php
// catalog/model/extension/module/moysklad.php
class ModelExtensionModuleMoysklad extends Model {
    // Простой пример: при создании заказа вызывается отправка в МойСклад
    public function onOrderAdd($route, $args, $output) {
        // $args[0] обычно содержит данные заказа (в зависимости от события)
        $order_info = isset($args[0]) ? $args[0] : null;
        if (!$order_info) return;

        // Подготовка данных (пример)
        $payload = [
            'name' => 'Order #' . $order_info['order_id'],
            'externalCode' => (string)$order_info['order_id'],
            'sum' => $order_info['total'],
            'customer' => [
                'name' => trim($order_info['firstname'] . ' ' . $order_info['lastname'])
            ]
        ];

        // Используем admin модель для API-запроса (или дублируем код)
        // В простом варианте отправим через curl
        $api_url = rtrim($this->config->get('module_moysklad_api_url'), '/');
        $token = $this->config->get('module_moysklad_token');
        if (empty($api_url) || empty($token)) {
            $this->log->write('MoySklad: не настроен API URL или токен, заказ не отправлен');
            return;
        }

        $url = $api_url . '/api/remap/1.2/entity/customerorder';
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            $this->log->write('MoySklad order post error: ' . $response);
        } else {
            $this->log->write('MoySklad order sent for order_id ' . $order_info['order_id']);
        }
    }
}
