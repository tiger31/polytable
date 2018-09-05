<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class homework_check extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("SELECT COUNT(group_id) FROM homeworks WHERE `date`=:date AND lesson=:lesson AND group_id=:group_id");
        $this->type = Query::COUNT;
    }
}