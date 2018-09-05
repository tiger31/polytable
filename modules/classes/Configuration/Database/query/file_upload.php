<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class file_upload extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("UPDATE uploads SET stored_untill=:stored_untill WHERE name=:name");
        $this->type = Query::UPDATE;
    }
}