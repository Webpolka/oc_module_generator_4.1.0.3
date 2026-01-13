<?php

namespace Opencart\Catalog\Model\Extension\%ModuleName%\Module;

class %ModuleName% extends \Opencart\System\Engine\Model
{
    /**
     * Получение елементов одного екземпляра
     *
     * @param int|null $module_id Если указан, возвращает елементы только этого екземпляра
     * @return array
     */
    public function getItems(?int $module_id = null): array
{
    $language_id = (int)$this->config->get('config_language_id');

    $sql = "
        SELECT m.module_id, m.sort_order, md.title, md.content, md.status
        FROM `" . DB_PREFIX . "%name%` m
        LEFT JOIN `" . DB_PREFIX . "%name%_description` md
            ON m.item_id = md.item_id
        WHERE md.language_id = '" . $language_id . "'
          AND md.status = 1
          AND md.title <> ''
    ";

    if ($module_id) {
        $sql .= " AND m.module_id = '" . (int)$module_id . "'";
    }

    $sql .= " ORDER BY m.sort_order ASC, m.item_id ASC";

    $query = $this->db->query($sql);

    $all_items = [];

    foreach ($query->rows as $row) {
        $all_items[(int)$row['module_id']][] = $row;
    }

    //  ЗАЩИТА: если запрошен module_id и данных нет — вернуть пустой массив
    if ($module_id) {
        return $all_items[$module_id] ?? [];
    }

    return $all_items;
}

}
