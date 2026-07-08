<?php

namespace Library\Moysklad;

class Attribute
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createOrUpdateAttribute($name, $type = 'text'): int
    {
        $query = $this->db->query("
            SELECT attribute_id FROM " . DB_PREFIX . "attribute WHERE attribute_name = '" . $this->db->escape($name) . "'
        ");

        if ($query->row) {
            return $query->row['attribute_id'];
        }

        $this->db->query("
            INSERT INTO " . DB_PREFIX . "attribute SET attribute_name = '" . $this->db->escape($name) . "', attribute_type = '" . $this->db->escape($type) . "'
        ");

        return $this->db->getLastId();
    }

    public function addProductAttribute($productId, $attributeId, $value): void
    {
        $this->db->query("
            INSERT INTO " . DB_PREFIX . "product_attribute_value 
            SET product_id = '" . (int)$productId . "', attribute_id = '" . (int)$attributeId . "', text = '" . $this->db->escape($value) . "'
        ");
    }
}
