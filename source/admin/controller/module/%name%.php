<?php

namespace Opencart\Admin\Controller\Extension\%ModuleName%\Module;

class %ModuleName% extends \Opencart\System\Engine\Controller
{
    /**
     * Установка расширения
     */
    public function install(): void
    {
        $this->load->model('setting/event');
        $this->load->model('extension/%name%/module/%name%');

        // Добавляем события
        $events = [
            // Добавляем в меню админки
            [
                'code' => '%name%_admin',
                'description' => 'Add %ModuleName% to admin sidebar',
                'trigger' => 'admin/view/common/column_left/before',
                'action' => 'extension/%name%/event/%name%',
                'status' => 1,
                'sort_order' => 0
            ],            
            // Вывод каких то стилей или скриптов или другого в шапке
            [
                'code'        => '%name%_header_before',
                'description' => 'вывод controller headerBefore',
                'trigger'     => 'catalog/controller/common/header/before',
                'action'      => 'extension/%name%/event/%name%.headerBefore',
                'status'      => 1,
                'sort_order'  => 0
            ],
            // Вывод каких то стилей или скриптов или другого в подвале
            [
                'code'        => '%name%_footer_before',
                'description' => 'вывод controller footerBefore',
                'trigger'     => 'catalog/controller/common/footer/before',
                'action'      => 'extension/%name%/event/%name%.footerBefore',
                'status'      => 1,
                'sort_order'  => 0
            ]
        ];

        foreach ($events as $event) {
            $this->model_setting_event->deleteEventByCode($event['code']);
            $this->model_setting_event->addEvent($event);
        }

        // Создаем таблицы и первый модуль
        $this->model_extension_%name%_module_%name%->install();
    }

    /**
     * Деинсталляция расширения
     */
    public function uninstall(): void
    {
        $this->load->model('setting/event');
        $this->load->model('extension/%name%/module/%name%');

        foreach (['%name%_admin', '%name%_header_before', '%name%_footer_after'] as $code) {
            $this->model_setting_event->deleteEventByCode($code);
        }

        $this->model_extension_%name%_module_%name%->uninstall();
    }

    /**
     * Список аккордеонов
     */
    public function index(): void
    {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->language('extension/%name%/module/%name%');       
        $this->load->model('extension/%name%/module/%name%');
        $this->load->model('setting/module');
        $this->load->model('localisation/language');

        $module_id = (int)($this->request->get['module_id'] ?? 0);

        // Если модуля нет — создаем новый
        if (!$module_id) {
            $existing = $this->model_setting_module->getModulesByCode('%name%.%name%');
            $count = count($existing) + 1;

            // 1) Создаём новый экземпляр
            $module_id = $this->model_setting_module->addModule('%name%.%name%', [
                'name' => '%ModuleName% ' . $count,
                'status' => 1
            ]);

            // 2) Сразу обновляем, добавляя module_id в settings
            $this->model_setting_module->editModule($module_id, [
                'name' => '%ModuleName% ' . $count,
                'status' => 1,
                'module_id' => $module_id
            ]);           
        }


        $module_info = $this->model_setting_module->getModule($module_id) ?? [];
        $languages = $this->model_localisation_language->getLanguages();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['name'] = $module_info['name'] ?? '';
        $data['status'] = $module_info['status'] ?? 1;
        $data['module_id'] = $module_id;
        $data['user_token'] = $this->session->data['user_token'];
        $data['languages'] = $languages;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

         // Ссылки для изменения заголовка экземпляра
        $data['action_rename'] = $this->url->link(
            'extension/%name%/module/%name%.saveInstance',
            'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id,
            true
        );

         // Ссылки для удаления екземпляра
        $data['action_delete'] = $this->url->link(
            'extension/%name%/module/%name%.deleteInstance',
            'user_token=' . $this->session->data['user_token'],
            true
        );

        // Ссылки для перехода на страницу модулей
        $data['modules_url'] = $this->url->link(
            'marketplace/extension',
            'user_token=' . $this->session->data['user_token'] . '&type=module',
            true
        );

        
        $this->response->setOutput(
            $this->load->view('extension/%name%/module/%name%', $data)
        );
    }

    
    /**
     * Сохраняем данные екземпляра
     */
  public function saveInstance(): void
{
    $this->load->model('setting/module');
    $json = [];

    if (!$this->user->hasPermission('modify', 'extension/%name%/module/%name%')) {
        $json['error'] = 'Нет прав для изменения';
    }

    if (!$json) {
        $module_id = (int)($this->request->post['module_id'] ?? 0);

        if (!$module_id) {
            $json['error'] = 'module_id не передан';
        } else {
            $post = [
                'name'      => $this->request->post['name'] ?? '%ModuleName%',
                'status'    => $this->request->post['status'] ?? 1,
                'module_id' => $module_id // КРИТИЧЕСКИ ВАЖНО
            ];

            $this->model_setting_module->editModule($module_id, $post);

            $json['success'] = true;
            $json['module_id'] = $module_id;
        }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}




    /**
     * Удаление екземпляра
     */
    public function deleteInstance(): void
    {
        $this->load->language('extension/%name%/module/%name%');
        $this->load->model('setting/module');

        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/%name%/module/%name%')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $module_id = (int) ($this->request->post['module_id'] ?? 0);

            if ($module_id) {
                $this->model_setting_module->deleteModule($module_id);
                $json['success'] = true;
            } else {
                $json['error'] = 'Некорректный module_id';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
