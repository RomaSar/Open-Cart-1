<?php
// admin/model/extension/module/moysklad.php
class ModelExtensionModuleMoysklad extends Model {
    // Отправка HTTP-запроса в API МойСклад
    protected function apiRequest($method, $path, $data = null) {
        $api_url = rtrim($this->config->get('module_moysklad_api_url'), '/');
        $token = $this->config->get('module_moysklad_token');

        if (empty($api_url) || empty($token)) {
            throw new Exception('MoySklad API URL или токен не настроены');
        }

        $url = $api_url . $path;
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            throw new Exception('cURL error: ' . $error);
        }
        if ($status < 200 || $status >= 300) {
            throw new Exception('MoySklad API returned status ' . $status . ': ' . $response);
        }
        return json_decode($response, true);
    }

    // Пример синхронизации товаров: отправляем базовую информацию о товарах
    public function syncProducts() {
        // Получим несколько товаров для примера
        $query = $this->db->query("SELECT product_id, model, sku, price, quantity FROM " . DB_PREFIX . "product LIMIT 50");
        $count = 0;
        foreach ($query->rows as $row) {
            $payload = [
                'name' => $row['model'] ?: ('product-' . $row['product_id']),
                'code' => $row['sku'],
                'salePrices' => [
                    ['value' => $row['price'], 'currency' => 'RUB']
                ],
                'stock' => $row['quantity']
            ];

            // В MoySklad нужно работать с сущностью 'product' или 'product' -> используйте нужные эндпойнты API
            // Здесь — условный пример: POST /api/remap/1.2/entity/product
            try {
                $this->apiRequest('POST', '/api/remap/1.2/entity/product', $payload);
                $count++;
            } catch (Exception $e) {
                // Логирование ошибки, но не прерывать весь процесс
                $this->log->write('MoySklad product sync error: ' . $e->getMessage());
            }
        }
        return "Отправлено товаров (попыток): " . $count;
    }

    // Пример отправки новых (или последних) заказов
    public function syncOrders() {
        // Получим последние неоправленные заказы (пример: последние 20)
        $query = $this->db->query("SELECT order_id, firstname, lastname, total, currency_code FROM " . DB_PREFIX . "order ORDER BY date_added DESC LIMIT 20");
        $count = 0;
        foreach ($query->rows as $row) {
            $payload = [
                'name' => 'Order #' . $row['order_id'],
                'sum' => $row['total'],
                'externalCode' => (string)$row['order_id'],
                'customer' => [
                    'name' => trim($row['firstname'] . ' ' . $row['lastname'])
                ],
                'currency' => $row['currency_code']
            ];

            try {
                $this->apiRequest('POST', '/api/remap/1.2/entity/customerorder', $payload);
                $count++;
            } catch (Exception $e) {
                $this->log->write('MoySklad order sync error: ' . $e->getMessage());
            }
        }
        return "Отправлено заказов (попыток): " . $count;
    }
}
