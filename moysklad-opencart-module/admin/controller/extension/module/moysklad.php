<?php
// admin/controller/extension/module/moysklad.php
class ControllerExtensionModuleMoysklad extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/moysklad');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        // Сохранение настроек
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('module_moysklad', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'type=module', true));
        }

        $data = array();
        $data['heading_title'] = $this->language->get('heading_title');

        // Поля настроек
        $fields = array('module_moysklad_status', 'module_moysklad_api_url', 'module_moysklad_token', 'module_moysklad_shop_uuid');
        foreach ($fields as $field) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
            } else {
                $data[$field] = $this->config->get($field);
            }
        }

        // Ссылки
        $data['action'] = $this->url->link('extension/module/moysklad', '', true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'type=module', true);

        // Кнопки синхронизации (AJAX роуты)
        $data['sync_products'] = $this->url->link('extension/module/moysklad.syncProducts', '', true);
        $data['sync_orders'] = $this->url->link('extension/module/moysklad.syncOrders', '', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/moysklad', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/moysklad')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    // AJAX: синхронизация товаров
    public function syncProducts() {
        $this->load->language('extension/module/moysklad');
        $this->load->model('extension/module/moysklad');

        try {
            $result = $this->model_extension_module_moysklad->syncProducts();
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['success' => true, 'message' => $result]));
        } catch (Exception $e) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    // AJAX: синхронизация заказов (отправка новых заказов в МойСклад)
    public function syncOrders() {
        $this->load->language('extension/module/moysklad');
        $this->load->model('extension/module/moysklad');

        try {
            $result = $this->model_extension_module_moysklad->syncOrders();
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['success' => true, 'message' => $result]));
        } catch (Exception $e) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function install() {
        // Зарегистрируем событие при добавлении заказа — чтобы отправлять в МойСклад
        $this->load->model('setting/event');
        $this->model_setting_event->addEvent('moysklad_order_add', 'catalog/model/checkout/order/addOrder/after', 'extension/module/moysklad->onOrderAdd');
    }

    public function uninstall() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('moysklad_order_add');
    }
}
