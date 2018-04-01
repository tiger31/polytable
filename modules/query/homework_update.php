<?php


class homework_update extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("UPDATE homeworks SET sender_id=:sender_id, text=:text WHERE group_id=:group_id AND `date`=:date AND lesson=:lesson");
        $this->type = db_query::UPDATE;
    }
}