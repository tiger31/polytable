<?php
use Configuration\Database\Connection\Query;
class user_update extends Query{
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $ip = array(
            "keys" => array("ip", "id"),
            "query" => $mysql->prepare("UPDATE users SET last_ip=:ip WHERE id=:id")
        );
        $session = array(
            "keys" => array("login", "sseed"),
            "query" => $mysql->prepare("UPDATE users SET session_hash=:sseed WHERE login=:login")
        );
        $active = array(
            "keys" => array("login"),
            "query" => $mysql->prepare("UPDATE users SET active=1 WHERE login=:login")
        );
        $verify = array(
            "keys" => array("id"),
            "query" => $mysql->prepare("UPDATE users SET verified=1 WHERE id=:id")
        );
        $email = array(
            "keys" => array("login", "email"),
            "query" => $mysql->prepare("UPDATE users SET email=:email WHERE login=:login")
        );
        $pass = array(
            "keys" => array("login", "pass"),
            "query" => $mysql->prepare("UPDATE users SET password_hash=:pass WHERE login=:login")
        );
        $number = array(
            "keys" => array("login", "number"),
            "query" => $mysql->prepare("UPDATE users SET `number`=:number WHERE login=:login")
        );
        $vk = array(
            "keys" => array("login", "vk_id"),
            "query" => $mysql->prepare("UPDATE users SET vk_id=:vk_id WHERE login=:login")
        );
        array_push($this->multiple, $ip, $active, $verify, $email, $pass, $number, $session, $vk);
        $this->type = Query::UPDATE;
    }
};