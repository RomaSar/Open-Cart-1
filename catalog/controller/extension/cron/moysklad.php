<?php

namespace Opencart\Catalog\Controller\Extension\Cron;

class Moysklad extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        $this->load->library('moysklad');
        $this->load->model('extension/module/moysklad');

        $moysklad = new \Library\Moysklad($this->config, $this->registry);
        $moysklad->sync();
    }
}
