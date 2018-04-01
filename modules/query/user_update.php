<?php

class user_update extends db_query{
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $ip = array(
            "keys" => array("ip", "id"),
            "query" => $mysql->prepare("UPDATE users SET last_ip=:ip WHERE id=:id")
        );
        $info = array(
            "keys" => array("login", "email", "number", "vk_link"),
            "query" => $mysql->prepare("UPDATE users SET email=:email, vk_link=:vk_link, `number`=:number WHERE login=:login")
        );
        $active = array(
            "keys" => array("login"),
            "query" => $mysql->prepare("UPDATE users SET active=1 WHERE login=:login")
        );
        $verify = array(
            "keys" => array("id"),
            "query" => $mysql->prepare("UPDATE users SET verified=1 WHERE id=:id")
        );
        array_push($this->multiple, $ip, $info, $active, $verify);
        $this->type = db_query::UPDATE;
    }
};