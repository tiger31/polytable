<?php

class homework_insert extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO homeworks (group_id, sender_id, `date`, lesson, text) VALUES 
          (:group_id, :sender_id, :date, :lesson, :text) ON DUPLICATE KEY UPDATE text=:text");
        $this->type = db_query::INSERT;
    }
}