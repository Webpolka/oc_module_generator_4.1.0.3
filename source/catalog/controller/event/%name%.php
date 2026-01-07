<?php

namespace Opencart\Catalog\Controller\Extension\%ModuleName%\Event;

class %ModuleName% extends \Opencart\System\Engine\Controller
{

    /**
     * Главная функция
     */
    public function index(&$route, &$args): void{}          
    /**
     * BEFORE: Получаем аргументы по ссылке и модифицируем их
     *
     * string $route   — имя маршрута (например, catalog/controller/common/header/before)
     * array  &$args   — аргументы, которые передаются в контроллер
     */
    public function headerBefore(string &$route, array &$args): void
    {
        // Добавляем CSS-файл в массив $args, если он передан
        // Но чаще в header можно напрямую использовать document
        $this->document->addStyle('extension/%name%/catalog/view/stylesheet/%name%.css');
    }
    /**
     * AFTER: Получаем аргументы по ссылке и модифицируем их
     *
     * string $route   — имя маршрута (например, catalog/controller/common/footer/after)
     * array  &$args   — аргументы, которые передаются в контроллер
     */
    public function footerBefore(string &$route, array &$args): void
    {
        // Добавляем JS-файл в массив $args, если он передан
        // Но чаще в header можно напрямую использовать document
        $this->document->addScript('extension/%name%/catalog/view/javascript/%name%.js', 'footer');
        
    }
}