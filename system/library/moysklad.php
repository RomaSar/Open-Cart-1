<?php

namespace Library;

class Moysklad
{
    private $api;
    private $sync;
    private $config;
    private $registry;

    public function __construct($config, $registry)
    {
        $this->config = $config;
        $this->registry = $registry;
        $this->api = new \Library\Moysklad\Api($config, $registry);
        $this->sync = new \Library\Moysklad\Sync($config, $registry);
    }

    public function sync(): array
    {
        $result = [
            'categories' => 0,
            'products' => 0,
            'prices' => 0,
            'stocks' => 0,
            'images' => 0,
            'attributes' => 0,
            'errors' => []
        ];

        try {
            $result['categories'] = $this->sync->syncCategories();
            $result['products'] = $this->sync->syncProducts();
            $result['prices'] = $this->sync->syncPrices();
            $result['stocks'] = $this->sync->syncStocks();
            
            if ($this->config->get('module_moysklad_sync_images')) {
                $result['images'] = $this->sync->syncImages();
            }
            
            if ($this->config->get('module_moysklad_sync_attributes')) {
                $result['attributes'] = $this->sync->syncAttributes();
            }
        } catch (\Exception $e) {
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    public function testConnection(): bool
    {
        return $this->api->testConnection();
    }
}
