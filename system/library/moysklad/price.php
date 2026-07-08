<?php

namespace Library\Moysklad;

class Price
{
    private $db;
    private $priceType;

    public function __construct($db, $priceType = 'selling')
    {
        $this->db = $db;
        $this->priceType = $priceType;
    }

    public function updateProductPrice($productId, $price, $currencyCode = 'RUB'): void
    {
        $this->db->query("
            UPDATE " . DB_PREFIX . "product SET price = '" . (float)$price . "' WHERE product_id = '" . (int)$productId . "'
        ");
    }

    public function getProductPrice($productId): float
    {
        $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$productId . "'");
        return (float)($query->row['price'] ?? 0);
    }
}
