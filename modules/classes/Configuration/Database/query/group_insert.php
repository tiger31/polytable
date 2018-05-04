<?php
use Configuration\Database\Connection\Query;
class group_insert extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO groups (name, id, university_id) VALUES (:name, :id, :university_id)");
        $this->type = Query::INSERT;
    }
};