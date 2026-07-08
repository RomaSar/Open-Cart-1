<?php

namespace Library\Moysklad;

class Order
{
    private $db;
    private $api;

    public function __construct($db, $api)
    {
        $this->db = $db;
        $this->api = $api;
    }

    public function sendOrderToMoyskiad($orderId): bool
    {
        $query = $this->db->query("
            SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$orderId . "'
        ");

        if (!$query->row) {
            return false;
        }

        $order = $query->row;
        $orderData = [
            'name' => $order['firstname'] . ' ' . $order['lastname'],
            'description' => 'Order #' . $orderId,
            'applicable' => true
        ];

        try {
            $this->api->createOrder($orderData);
            return true;
        } catch (\Exception $e) {
            error_log('Send order to Moysklad error: ' . $e->getMessage());
            return false;
        }
    }
}
