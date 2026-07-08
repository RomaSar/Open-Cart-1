<?php
class LibraryMysklad {
    protected $apiUrl = 'https://online.moysklad.ru/api/remap/1.2';
    protected $secret;

    public function __construct($secret = null) {
        $this->secret = $secret;
    }

    // Заглушка запроса к API
    public function request($path, $method = 'GET', $data = null) {
        // TODO: Реализовать CURL/HTTP клиент
        return ['status' => 'stub', 'path' => $path];
    }

    // Примеры методов: получить остатки, цены, товар
    public function getStocks() { return $this->request('/entity/stock'); }
    public function getPrices() { return $this->request('/entity/assortment'); }
}
