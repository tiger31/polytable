<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class file_select extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("SELECT * FROM uploads WHERE name=:name");
        $this->type = Query::SELECT;
    }
}