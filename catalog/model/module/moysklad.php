<?php

namespace Opencart\Catalog\Model\Module;

class Moysklad extends \Opencart\System\Engine\Model
{
    public function getSettings(): array
    {
        $this->load->model('setting/module');
        return $this->model_setting_module->getSetting('module_moysklad');
    }
}
