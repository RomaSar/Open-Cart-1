<?php

namespace Library\Moysklad;

class Category
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createOrUpdateCategory($msId, $data): int
    {
        $query = $this->db->query("
            SELECT category_id FROM " . DB_PREFIX . "moysklad_category WHERE ms_id = '" . $this->db->escape($msId) . "'
        ");

        if ($query->row) {
            $categoryId = $query->row['category_id'];
        } else {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "moysklad_category SET ms_id = '" . $this->db->escape($msId) . "', created_at = NOW()
            ");
            $categoryId = $this->db->getLastId();
        }

        return $categoryId;
    }
}
