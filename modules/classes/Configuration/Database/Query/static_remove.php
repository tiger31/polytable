<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class static_remove extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("DELETE FROM calendar_static WHERE group_id=:group_id AND weekday=:weekday AND lesson=:lesson AND is_odd=:is_odd");
        $this->type = Query::DELETE;
    }
}
