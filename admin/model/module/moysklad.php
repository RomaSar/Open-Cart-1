<?php

namespace Opencart\Admin\Model\Module;

class Moysklad extends \Opencart\System\Engine\Model
{
    public function getLastSync(): ?string
    {
        if ($this->db->tableExists(DB_PREFIX . 'moysklad_sync')) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "moysklad_sync ORDER BY sync_date DESC LIMIT 1");
            return $query->row['sync_date'] ?? null;
        }
        return null;
    }

    public function addSync($data): void
    {
        $this->createTables();
        $this->db->query("INSERT INTO " . DB_PREFIX . "moysklad_sync SET sync_date = NOW(), data = '" . $this->db->escape(json_encode($data)) . "'");
    }

    public function createTables(): void
    {
        if (!$this->db->tableExists(DB_PREFIX . 'moysklad_sync')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moysklad_sync` (
                  `sync_id` int(11) NOT NULL AUTO_INCREMENT,
                  `sync_date` datetime NOT NULL,
                  `data` longtext,
                  PRIMARY KEY (`sync_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->tableExists(DB_PREFIX . 'moysklad_product')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moysklad_product` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `ms_id` varchar(255) NOT NULL,
                  `product_id` int(11),
                  `sku` varchar(255),
                  `created_at` datetime,
                  `updated_at` datetime,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->tableExists(DB_PREFIX . 'moysklad_category')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moysklad_category` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `ms_id` varchar(255) NOT NULL,
                  `category_id` int(11),
                  `created_at` datetime,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }
}
