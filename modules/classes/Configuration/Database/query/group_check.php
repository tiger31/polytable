<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class group_check extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("SELECT COUNT(id) FROM groups WHERE id=:id OR name=:name");
        $this->type = Query::COUNT;
    }
};