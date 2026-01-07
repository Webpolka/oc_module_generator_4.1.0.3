<?php
namespace Opencart\Catalog\Controller\Extension\%ModuleName%\Module;

class %ModuleName% extends \Opencart\System\Engine\Controller {


     public function index(array $setting): string
    {
        $this->load->model('extension/%name%/module/%name%');

        $instance_id = (int)($setting['module_id'] ?? 0);

        $data['items'] = $this->model_extension_%name%_module_%name%
            ->getItems($instance_id);
        
        $data['id'] = $instance_id;
        $data['name'] = $setting['name'] ?? "";

        return $this->load->view('extension/%name%/module/%name%', $data);
    }
}
