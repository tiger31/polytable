<?php
use Configuration\Database\Connection\Query;
class homework_insert extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO homeworks (group_id, sender_id, `date`, lesson, text) VALUES 
          (:group_id, :sender_id, :date, :lesson, :text) ON DUPLICATE KEY UPDATE text=:text");
        $this->type = Query::INSERT;
    }
}