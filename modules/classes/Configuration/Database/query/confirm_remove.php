<?php
use Configuration\Database\Connection\Query;
class confirm_remove extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("DELETE FROM confirm_hash WHERE login=:login AND `value`=:hash");
        $this->type = Query::DELETE;
    }
}