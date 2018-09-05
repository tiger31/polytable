<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class dynamic_select extends Query {
    function prepare(\PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array(
            "keys" => array("group_id"),
            "query" => $mysql->prepare("SELECT * FROM calendar_dynamic WHERE `group_id`=:group_id ORDER BY `day`, `lesson` ASC")
        );
        array_push($this->multiple, $select_all);
        $this->type = Query::SELECT;
    }
}