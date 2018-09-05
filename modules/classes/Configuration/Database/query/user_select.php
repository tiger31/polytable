<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class user_select extends Query {
    function prepare(\PDO $mysql) {
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
        $select_by_vk_id = array(
            "keys" => array("vk_id"),
            "query" => $mysql->prepare("SELECT * FROM users WHERE vk_id=:vk_id")
        );
        $select_by_email = array(
            "keys" => array("email"),
            "query" => $mysql->prepare("SELECT * FROM users WHERE email=:email")
        );
        $select_session = array(
            "keys" => array("sseed"),
            "query" => $mysql->prepare("SELECT * FROM users WHERE session_hash=:sseed")
        );
        array_push($this->multiple, $select_single, $select_group, $select_max, $id, $select_by_email, $select_by_vk_id, $select_session);
        $this->type = Query::SELECT;
    }
}