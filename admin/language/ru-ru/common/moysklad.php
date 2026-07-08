<?php

namespace Opencart\Admin\Language\Ru_ru\Module;

class Moysklad
{
    public function get($key)
    {
        $data = [
            'menu_moysklad' => 'Мой Склад'
        ];
        return $data[$key] ?? $key;
    }
}
