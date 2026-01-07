<?php

namespace Opencart\Admin\Model\Extension\%ModuleName%\Module;

class %ModuleName% extends \Opencart\System\Engine\Model
{ 
    /**
     * Установка таблиц и модуля
     */
   public function install(): void
{
    // Основная таблица 
    $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "%name%` (
            `item_id` INT(11) NOT NULL AUTO_INCREMENT,
            `module_id` INT(11) NOT NULL,
            `sort_order` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Таблица мультиязычных описаний
    $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "%name%_description` (
            `item_id` INT(11) NOT NULL,
            `module_id` INT(11) NOT NULL,
            `language_id` INT(11) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `content` TEXT NOT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`item_id`,`module_id`,`language_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    
    // Создаём первый экземпляр модуля
    $this->load->model('setting/module');

    // 1) Сначала добавляем екземпляр и получаем module_id
    $module_id = $this->model_setting_module->addModule('%name%.%name%', [
        'name' => '%ModuleName% 1',
        'status' => 1
    ]);

    // 2) Сразу обновляем этот же екземпляр, добавляя module_id в массив настроек
    $this->model_setting_module->editModule($module_id, [
        'name' => '%ModuleName% 1',
        'status' => 1,
        'module_id' => $module_id
    ]);
}

    /**
     * Деинсталляция
     */
    public function uninstall(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "%name%_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "%name%`");
    }
}
