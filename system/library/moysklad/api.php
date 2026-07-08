<?php

namespace Library\Moysklad;

class Api
{
    private $baseUrl = 'https://api.moysklad.ru/api/remap/1.2';
    private $apiKey = '';
    private $config;
    private $registry;

    public function __construct($config, $registry)
    {
        $this->config = $config;
        $this->registry = $registry;
        $this->apiKey = $config->get('module_moysklad_api_key');
    }

    public function testConnection(): bool
    {
        try {
            $response = $this->request('/entity/counterparty');
            return !empty($response);
        } catch (\Exception $e) {
            throw new \Exception('Ошибка подключения: ' . $e->getMessage());
        }
    }

    public function getCategories($limit = 100, $offset = 0): array
    {
        return $this->request('/entity/productfolder', [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function getProducts($limit = 100, $offset = 0): array
    {
        return $this->request('/entity/product', [
            'limit' => $limit,
            'offset' => $offset,
            'expand' => 'attributes,uom,supplier'
        ]);
    }

    public function getPrices($productId, $limit = 100, $offset = 0): array
    {
        return $this->request("/entity/product/{$productId}/salePrices", [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function getStock($productId): array
    {
        return $this->request("/entity/product/{$productId}/stock");
    }

    public function getImages($productId): array
    {
        return $this->request("/entity/product/{$productId}/images");
    }

    public function createOrder($data): array
    {
        return $this->request('/entity/order', $data, 'POST');
    }

    private function request($endpoint, $params = [], $method = 'GET'): array
    {
        $url = $this->baseUrl . $endpoint;
        
        if (!empty($params) && $method === 'GET') {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ':');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new \Exception("API Error: HTTP $httpCode - $response");
        }

        return json_decode($response, true) ?? [];
    }
}
