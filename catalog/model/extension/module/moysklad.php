<?php

namespace Opencart\Catalog\Model\Extension\Module;

class Moysklad extends \Opencart\System\Engine\Model
{
    public function getSettings(): array
    {
        $this->load->model('setting/setting');
        return $this->model_setting_setting->getSetting('module_moysklad');
    }
}
