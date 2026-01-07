<?php

namespace Opencart\Admin\Controller\Extension\%ModuleName%\Event;

class %ModuleName% extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(&$route, &$args): void
    {

        $this->load->language('extension/%name%/module/%name%');
        $this->load->model('setting/module');

        // Получаем все экземпляры 
        $existing_modules = $this->model_setting_module->getModulesByCode('%name%.%name%');

        $children = [];
        foreach ($existing_modules as $mod) {
            $children[] = [
                'name' => $mod['name'],
                'href' => $this->url->link(
                    'extension/%name%/module/%name%',
                    'user_token=' . $this->session->data['user_token'] . '&module_id=' . $mod['module_id'],
                    true
                ),
                'children' => []
            ];
        }

        // Добавляем подпункт "+ Добавить новый"
        $children[] = [
            'name' => $this->language->get('add_new'), // мультиязычный'+ Добавить новое',
            'href' => $this->url->link(
                'extension/%name%/module/%name%',
                'user_token=' . $this->session->data['user_token'],
                true
            ),
            'children' => []
        ];

        // Добавляем основной пункт меню с вложениями
        $args['menus'][] = [
            'id'       => '%ModuleName%-extension',
            'icon'     => 'fa-solid fa-puzzle-piece',
            'name'     => '%ModuleName%',
            'href'     => "", // делаем заголовок не кликабельным
            'children' => $children
        ];
    }
}

