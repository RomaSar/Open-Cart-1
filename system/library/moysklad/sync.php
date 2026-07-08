<?php

namespace Library\Moysklad;

class Sync
{
    private $api;
    private $db;
    private $config;
    private $registry;

    public function __construct($config, $registry)
    {
        $this->config = $config;
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->api = new Api($config, $registry);
    }

    public function syncCategories(): int
    {
        $count = 0;
        try {
            $categories = $this->api->getCategories(100, 0);
            foreach ($categories['rows'] ?? [] as $category) {
                $this->syncCategory($category);
                $count++;
            }
        } catch (\Exception $e) {
            error_log('Sync categories error: ' . $e->getMessage());
        }
        return $count;
    }

    public function syncProducts(): int
    {
        $count = 0;
        try {
            $offset = 0;
            do {
                $products = $this->api->getProducts(100, $offset);
                foreach ($products['rows'] ?? [] as $product) {
                    $this->syncProduct($product);
                    $count++;
                }
                $offset += 100;
            } while (count($products['rows'] ?? []) === 100);
        } catch (\Exception $e) {
            error_log('Sync products error: ' . $e->getMessage());
        }
        return $count;
    }

    public function syncPrices(): int
    {
        $count = 0;
        try {
            $products = $this->db->query("SELECT ms_id FROM " . DB_PREFIX . "moysklad_product");
            foreach ($products->rows as $product) {
                $prices = $this->api->getPrices($product['ms_id']);
                foreach ($prices['rows'] ?? [] as $price) {
                    $this->syncPrice($product['ms_id'], $price);
                    $count++;
                }
            }
        } catch (\Exception $e) {
            error_log('Sync prices error: ' . $e->getMessage());
        }
        return $count;
    }

    public function syncStocks(): int
    {
        $count = 0;
        try {
            $products = $this->db->query("SELECT ms_id, product_id FROM " . DB_PREFIX . "moysklad_product");
            foreach ($products->rows as $product) {
                $stock = $this->api->getStock($product['ms_id']);
                $this->updateStock($product['product_id'], $stock);
                $count++;
            }
        } catch (\Exception $e) {
            error_log('Sync stocks error: ' . $e->getMessage());
        }
        return $count;
    }

    public function syncImages(): int
    {
        $count = 0;
        try {
            $products = $this->db->query("SELECT ms_id, product_id FROM " . DB_PREFIX . "moysklad_product");
            foreach ($products->rows as $product) {
                $images = $this->api->getImages($product['ms_id']);
                foreach ($images['rows'] ?? [] as $image) {
                    $this->syncImage($product['product_id'], $image);
                    $count++;
                }
            }
        } catch (\Exception $e) {
            error_log('Sync images error: ' . $e->getMessage());
        }
        return $count;
    }

    public function syncAttributes(): int
    {
        return 0; // Implementation
    }

    private function syncCategory($category): void
    {
        // Implementation
    }

    private function syncProduct($product): void
    {
        // Implementation
    }

    private function syncPrice($productMsId, $price): void
    {
        // Implementation
    }

    private function updateStock($productId, $stock): void
    {
        // Implementation
    }

    private function syncImage($productId, $image): void
    {
        // Implementation
    }
}
