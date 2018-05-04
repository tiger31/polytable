<?php
use Configuration\Database\Connection\Query;
class confirm_select  extends Query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select = array(
            "keys" => array("login", "hash"),
            "query" => $this->query = $mysql->prepare("SELECT * FROM confirm_hash WHERE login=:login AND `value`=:hash LIMIT 1")
        );
        $select_type = array(
            "keys" => array("login", "type"),
            "query" => $mysql->prepare("SELECT * FROM confirm_hash WHERE login=:login AND `for`=:type LIMIT 1")
        );
        array_push($this->multiple, $select, $select_type);
        $this->type = Query::SELECT;
    }
}