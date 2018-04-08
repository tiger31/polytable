<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $local_modules_path . "/Connect.php";
include_once $local_modules_path . "/Security.php";
global $mysql;
$mysql->set_active(QUERY_GROUP_SELECT, QUERY_CONTRIBUTOR_CHECK);

class User {
    public $id;
    public $email;
    public $login;
    public $first_name;
    public $second_name;
    public $number;
    public $vk_link;
    public $is_head;
    public $is_contributor;
    public $group;
    public $group_id;
    public $privileges;
    public $verified;

    function __construct($data) {
        global $mysql;
        $this->id = $data['id'];
        $this->login = $data['login'];
        $this->number = $data['number'];
        $this->vk_link = $data['vk_link'];
        $this->email = $data["email"];
        $this->is_head = ($data['is_head'] == 1) ? true : false;
        $this->group = $data['group'];
        $this->privileges = (int)$data['privileges'];
        $this->group_id = $mysql->get_group($this->group)['id'];
        $this->is_contributor = $this->check_contributor();
        $this->verified = (bool)$data['verified'];

    }

    function getID() {
        return $this->id;
    }
    function getEscapedName() {
        return escape_string($this->login);
    }
    function getPost() {
        if ($this->is_head) {
            return "Староста " . $this->group;
        } else {
            switch ($this->privileges) {
                case 0:
                    return "Студент";
                    break;
                case 1:
                    return "Модератор";
                    break;
                case 2:
                    return "Администратор";
                    break;
                default:
                    return "???";
                    break;
            }
        }
    }
    function getFullPost() {
        if ($this->is_head) {
            return "Староста группы " . $this->group;
        } else {
            switch ($this->privileges) {
                case 0:
                    return "Студент группы " . $this->group;
                    break;
                case 1:
                    return "Модератор";
                    break;
                case 2:
                    return "Администратор";
                    break;
                default:
                    return "???";
                    break;
            }
        }
    }
    function getPrivilegesLevel() {
        return $this->privileges;
    }

    function check_contributor() {
        global $mysql;
        return $mysql->exec(QUERY_CONTRIBUTOR_CHECK, RETURN_FALSE_ON_EMPTY, array("gr_id" => $this->group_id, "id" => $this->id));
    }

    public function group_editor() {
        return ($this->is_head || $this->is_contributor);
    }

    static function loadFromSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (session_check(true)) {
            return $_SESSION['user'];
        }
        return false;
    }
}
