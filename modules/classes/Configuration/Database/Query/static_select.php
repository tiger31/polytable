<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class static_select extends Query {
    function prepare(\PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array(
            "keys" => array("group_id"),
            "query" => $mysql->prepare("SELECT * FROM calendar_static WHERE `group_id`=:group_id ORDER BY `is_odd`, `weekday`, `lesson` ASC")
        );
        array_push($this->multiple, $select_all);
        $this->type = Query::SELECT;
    }
}