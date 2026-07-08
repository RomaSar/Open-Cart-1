<?php

namespace Opencart\Admin\Controller\Module;

class Moysklad extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        $this->load->language('module/moysklad');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('module/moysklad');
        $this->load->model('setting/module');

        $data = [];
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_api_key'] = $this->language->get('entry_api_key');
        $data['entry_sync_frequency'] = $this->language->get('entry_sync_frequency');
        $data['entry_price_type'] = $this->language->get('entry_price_type');
        $data['entry_sync_images'] = $this->language->get('entry_sync_images');
        $data['entry_sync_attributes'] = $this->language->get('entry_sync_attributes');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_sync_now'] = $this->language->get('button_sync_now');
        $data['button_test_connection'] = $this->language->get('button_test_connection');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_setting_module->editSetting('module_moysklad', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('module/moysklad', 'user_token=' . $this->session->data['user_token']));
        }

        $settings = $this->model_setting_module->getSetting('module_moysklad');
        $data['module_moysklad_status'] = $settings['module_moysklad_status'] ?? 0;
        $data['module_moysklad_api_key'] = $settings['module_moysklad_api_key'] ?? '';
        $data['module_moysklad_sync_frequency'] = $settings['module_moysklad_sync_frequency'] ?? 60;
        $data['module_moysklad_price_type'] = $settings['module_moysklad_price_type'] ?? 'selling';
        $data['module_moysklad_sync_images'] = $settings['module_moysklad_sync_images'] ?? 1;
        $data['module_moysklad_sync_attributes'] = $settings['module_moysklad_sync_attributes'] ?? 1;
        $data['module_moysklad_last_sync'] = $settings['module_moysklad_last_sync'] ?? '';

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('module/moysklad', 'user_token=' . $this->session->data['user_token'])
            ]
        ];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/moysklad', $data));
    }

    public function sync(): void
    {
        $this->load->model('module/moysklad');
        $this->load->library('moysklad');

        try {
            $moysklad = new \Library\Moysklad($this->config, $this->registry);
            $sync = $moysklad->sync();
            
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'status' => true,
                'message' => 'Синхронизация завершена',
                'data' => $sync
            ]));
        } catch (\Exception $e) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]));
        }
    }

    public function testConnection(): void
    {
        $this->load->library('moysklad');

        try {
            $moysklad = new \Library\Moysklad($this->config, $this->registry);
            $result = $moysklad->testConnection();
            
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'status' => true,
                'message' => 'Подключение успешно'
            ]));
        } catch (\Exception $e) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]));
        }
    }
}
