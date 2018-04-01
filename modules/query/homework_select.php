<?php

class homework_select extends db_query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array (
            "keys" => array("group_id"),
            "query" => $mysql->prepare("SELECT * FROM homeworks WHERE group_id=:group_id")
        );
        $select_single = array(
            "keys" => array("group_id", "date", "lesson"),
            "query" => $mysql->prepare("SELECT * FROM homeworks WHERE group_id=:group_id AND `date`=:date AND lesson=:lesson")
        );
        array_push($this->multiple, $select_single, $select_all);
        $this->type = db_query::SELECT;
    }
}