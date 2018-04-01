<?php

class group_update extends db_query{
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
        array_push($this->multiple, $cache, $id, $recache);
        $this->type = db_query::UPDATE;
    }
};