<?php

class homework_check extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT COUNT(group_id) FROM homeworks WHERE `date`=:date AND lesson=:lesson AND group_id=:group_id");
        $this->type = db_query::COUNT;
    }
}