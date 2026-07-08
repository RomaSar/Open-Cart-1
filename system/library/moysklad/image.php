<?php

namespace Library\Moysklad;

class Image
{
    private $db;
    private $config;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function downloadImage($productId, $imageUrl): string
    {
        $filename = md5($imageUrl) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
        $filepath = DIR_IMAGE . 'catalog/' . $filename;

        if (!file_exists(DIR_IMAGE . 'catalog/')) {
            mkdir(DIR_IMAGE . 'catalog/', 0755, true);
        }

        $imageData = file_get_contents($imageUrl);
        if ($imageData === false) {
            return '';
        }

        file_put_contents($filepath, $imageData);
        return 'catalog/' . $filename;
    }

    public function addProductImage($productId, $imagePath, $sort = 0): void
    {
        $this->db->query("
            INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$productId . "', image = '" . $this->db->escape($imagePath) . "', sort_order = '" . (int)$sort . "'
        ");
    }
}
