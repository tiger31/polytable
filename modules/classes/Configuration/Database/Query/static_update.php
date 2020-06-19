<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class static_update extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("UPDATE calendar_static SET id=:id WHERE group_id=:group_id AND is_odd=:is_odd AND weekday=:weekday AND lesson=:lesson");
        $this->type = Query::UPDATE;
    }
}