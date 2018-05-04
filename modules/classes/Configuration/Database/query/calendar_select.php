<?php
use Configuration\Database\Connection\Query;
class calendar_select extends Query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array(
            "keys" => array("group_id"),
            "query" => $mysql->prepare("SELECT calendar.*, homeworks.text FROM calendar LEFT JOIN homeworks ON calendar.group_id = homeworks.group_id AND calendar.day = homeworks.date AND calendar.lesson = homeworks.lesson WHERE calendar.group_id=:group_id  ORDER BY calendar.day, calendar.lesson ASC")
        );
        array_push($this->multiple, $select_all);
        $this->type = Query::SELECT;
    }
}