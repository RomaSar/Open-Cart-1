<?php

namespace Opencart\Admin\Language\En_gb\Module;

class Moysklad
{
    public function get($key)
    {
        $data = [
            'menu_moysklad' => 'MoySkiad'
        ];
        return $data[$key] ?? $key;
    }
}
