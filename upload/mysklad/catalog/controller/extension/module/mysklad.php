<?php
namespace Opencart\Catalog\Controller\Extension\Module;
class Mysklad extends \Opencart\System\Engine\Controller {
    // Пример ручной точки для экспорта каталога в МойСклад
    public function export(): void {
        // TODO: собрать товары, категории, фото, цены и отправить в МойСклад
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['status' => 'ok', 'exported' => 0]));
    }
}
