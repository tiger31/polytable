<?php
use Configuration\Database\Connection\Query;
class confirm_insert extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO confirm_hash (login, `for`, `value`, `stored`, lifetime) VALUES (:login, :for, :value, :stored, :lifetime)");
        $this->type = Query::INSERT;
    }
}