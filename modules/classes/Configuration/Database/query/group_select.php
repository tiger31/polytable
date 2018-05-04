<?php
use Configuration\Database\Connection\Query;
class group_select extends Query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array (
            "keys" => array(),
            "query" => $mysql->prepare("SELECT * FROM groups")
        );
        $search = array (
            "keys" => array("signature"),
            "query" => $mysql->prepare("SELECT name FROM groups WHERE name LIKE CONCAT(:signature, '%')")
        );
        $select = array(
            "keys" => array("name"),
            "query" => $mysql->prepare("SELECT * FROM groups WHERE name=:name LIMIT 1")
        );
        array_push($this->multiple, $select_all, $select, $search);
        $this->type = Query::SELECT;
    }
};