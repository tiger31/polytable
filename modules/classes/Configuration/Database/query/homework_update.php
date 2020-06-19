<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class homework_update extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("UPDATE homeworks SET sender_id=:sender_id, text=:text WHERE group_id=:group_id AND `date`=:date AND lesson=:lesson");
        $this->type = Query::UPDATE;
    }
}