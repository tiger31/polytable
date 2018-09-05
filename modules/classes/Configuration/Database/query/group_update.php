<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class group_update extends Query{
    function prepare(\PDO $mysql) {
        $this->is_multiple = true;
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
        $static_cache = array(
            "keys" => array("id", "static_changed"),
            "query" => $mysql->prepare("UPDATE groups SET static_changed=:static_changed, cache_static=1 WHERE id=:id"),
        );
        $cache = array(
            "keys" => array("id", "cache_last"),
            "query" => $mysql->prepare("UPDATE groups SET cache_last=:cache_last WHERE id=:id"),
        );
        array_push($this->multiple, $id, $recache, $refresh_counter, $static_cache, $cache);
        $this->type = Query::UPDATE;
    }
};