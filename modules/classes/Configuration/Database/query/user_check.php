<?php
use Configuration\Database\Connection\Query;
class user_check extends Query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $check_login = array(
            "keys" => array("login"),
            "query" => $mysql->prepare("SELECT COUNT(id) FROM users WHERE login=:login")
        );
        $check_email = array(
            "keys" => array("email"),
            "query" => $mysql->prepare("SELECT COUNT(id) FROM users WHERE email=:email")
        );
        array_push($this->multiple, $check_login, $check_email);
        $this->type = Query::COUNT;
    }
};