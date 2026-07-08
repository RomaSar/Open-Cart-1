<?php

namespace Opencart\Catalog\Controller\Cron;

class Moysklad extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        $this->load->library('moysklad');
        $this->load->model('module/moysklad');

        $moysklad = new \Library\Moysklad($this->config, $this->registry);
        $moysklad->sync();
    }
}
