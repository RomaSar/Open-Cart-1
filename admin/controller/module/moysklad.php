<?php

namespace Opencart\Admin\Controller\Module;

class Moysklad extends \Opencart\System\Engine\Controller
{
    private $error = [];

    public function index(): void
    {
        $this->load->language('module/moysklad');
        $this->load->model('setting/setting');
        $this->load->model('module/moysklad');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('module_moysklad', $this->request->post);
            $this->response->redirect($this->url->link('extension/module', 'user_token=' . $this->session->data['user_token']));
        }

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
        $data['text_home'] = $this->language->get('text_home');

        if (isset($this->request->post['module_moysklad_status'])) {
            $data['module_moysklad_status'] = $this->request->post['module_moysklad_status'];
        } else {
            $data['module_moysklad_status'] = $this->config->get('module_moysklad_status');
        }

        if (isset($this->request->post['module_moysklad_api_key'])) {
            $data['module_moysklad_api_key'] = $this->request->post['module_moysklad_api_key'];
        } else {
            $data['module_moysklad_api_key'] = $this->config->get('module_moysklad_api_key');
        }

        if (isset($this->request->post['module_moysklad_sync_frequency'])) {
            $data['module_moysklad_sync_frequency'] = $this->request->post['module_moysklad_sync_frequency'];
        } else {
            $data['module_moysklad_sync_frequency'] = $this->config->get('module_moysklad_sync_frequency') ?: 60;
        }

        if (isset($this->request->post['module_moysklad_price_type'])) {
            $data['module_moysklad_price_type'] = $this->request->post['module_moysklad_price_type'];
        } else {
            $data['module_moysklad_price_type'] = $this->config->get('module_moysklad_price_type') ?: 'selling';
        }

        if (isset($this->request->post['module_moysklad_sync_images'])) {
            $data['module_moysklad_sync_images'] = $this->request->post['module_moysklad_sync_images'];
        } else {
            $data['module_moysklad_sync_images'] = $this->config->get('module_moysklad_sync_images') ?: 1;
        }

        if (isset($this->request->post['module_moysklad_sync_attributes'])) {
            $data['module_moysklad_sync_attributes'] = $this->request->post['module_moysklad_sync_attributes'];
        } else {
            $data['module_moysklad_sync_attributes'] = $this->config->get('module_moysklad_sync_attributes') ?: 1;
        }

        $data['breadcrumbs'] = [
            [
                'text' => $data['text_home'],
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
            ],
            [
                'text' => $data['heading_title'],
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

    protected function validate()
    {
        return true;
    }
}
