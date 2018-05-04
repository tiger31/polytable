<?php
use Configuration\Database\Connection\Query;
class group_update extends Query{
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $cache = array(
            "keys" => array("until", "id"),
            "query" => $mysql->prepare("UPDATE groups SET cache_until=:until WHERE id=:id")
        );
        $id = array(
            "keys" => array("name", "id"),
            "query" => $mysql->prepare("UPDATE groups SET id=:id WHERE `name`=:name")
        );
        $recache = array (
            "keys" => array("name", "count"),
            "query" => $mysql->prepare("UPDATE groups SET recache_count=:count WHERE `name`=:name")
        );
        $refresh_counter = array(
            "keys" => array(),
            "query" => $mysql->prepare("UPDATE groups SET recache_count=5 WHERE cache=1"),
        );
        array_push($this->multiple, $cache, $id, $recache, $refresh_counter);
        $this->type = Query::UPDATE;
    }
};