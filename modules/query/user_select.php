<?php

class user_select extends db_query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select_single = array(
            "keys" => array("login"),
            "query" => $mysql->prepare("SELECT * FROM users WHERE login=:login")
        );
        $select_group = array(
            "keys" => array("group"),
            "query" => $mysql->prepare("SELECT id FROM users WHERE `group`=:group")
        );
        $id = array(
            "keys" => array("id"),
            "query" => $mysql->prepare("SELECT * FROM users WHERE id=:id")
        );
        $select_max = array(
            "keys" => array(),
            "query" => $mysql->prepare("SELECT MAX(id) FROM users")
        );
        array_push($this->multiple, $select_single, $select_group, $select_max, $id);
        $this->type = db_query::SELECT;
    }
};