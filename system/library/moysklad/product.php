<?php

namespace Library\Moysklad;

class Product
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createOrUpdateProduct($msId, $data): int
    {
        $query = $this->db->query("
            SELECT product_id FROM " . DB_PREFIX . "moysklad_product WHERE ms_id = '" . $this->db->escape($msId) . "'
        ");

        $sku = $data['code'] ?? '';
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';

        if ($query->row) {
            $productId = $query->row['product_id'];
            $this->db->query("
                UPDATE " . DB_PREFIX . "moysklad_product 
                SET sku = '" . $this->db->escape($sku) . "', updated_at = NOW() 
                WHERE ms_id = '" . $this->db->escape($msId) . "'
            ");
        } else {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "moysklad_product 
                SET ms_id = '" . $this->db->escape($msId) . "', sku = '" . $this->db->escape($sku) . "', created_at = NOW()
            ");
            $productId = $this->db->getLastId();
        }

        return $productId;
    }
}
