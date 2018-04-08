<?php

class calendar_select extends db_query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array(
            "keys" => array("group_id"),
            "query" => $mysql->prepare("SELECT calendar.*, homeworks.text FROM calendar LEFT JOIN homeworks ON calendar.group_id = homeworks.group_id AND calendar.day = homeworks.date AND calendar.lesson = homeworks.lesson WHERE calendar.group_id=:group_id  ORDER BY calendar.day, calendar.lesson ASC")
        );
        array_push($this->multiple, $select_all);
        $this->type = db_query::SELECT;
    }
}