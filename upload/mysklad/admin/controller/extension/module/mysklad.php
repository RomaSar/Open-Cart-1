<?php
namespace Opencart\Admin\Controller\Extension\Module;
class Mysklad extends \Opencart\System\Engine\Controller {
    public function index(): void {
        // Заглушка: простая страница настроек
        $this->load->language('extension/module/mysklad');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['product_id'] = $this->config->get('module_mysklad_product_id') ?? 'product_0001';
        $data['secret'] = $this->config->get('module_mysklad_secret') ?? '4f9c2a7d8e9b4c1fa6b2d3e8c9f0a1b2';

        $data['save'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $this->response->setOutput($this->load->view('extension/module/mysklad', $data));
    }

    // Вебхук для получения заказа из МойСклад (заглушка)
    public function webhook(): void {
        $json = file_get_contents('php://input');
        // TODO: проверка секретного ключа, парсинг заказа, создание заказа в OpenCart
        http_response_code(200);
        echo json_encode(['status' => 'ok', 'received' => strlen($json)]);
    }

    // Пример ручного синхрона остатков/цен
    public function sync(): void {
        // TODO: интеграция с API МойСклад — заглушка
        $this->load->model('extension/module/mysklad');
        $result = $this->model_extension_module_mysklad->syncProducts();
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }
}
