<?php

namespace Library\Moysklad;

class Stock
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function updateProductStock($productId, $quantity): void
    {
        $this->db->query("
            UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$productId . "'
        ");
    }

    public function getProductStock($productId): int
    {
        $query = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$productId . "'");
        return (int)($query->row['quantity'] ?? 0);
    }
}
