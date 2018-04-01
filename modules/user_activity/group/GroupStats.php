<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Connect.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/Module.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User.php";
global $mysql;
$mysql->set_active(QUERY_GROUP_SELECT, QUERY_CONTRIBUTOR_SELECT ,QUERY_HOMEWORK_SELECT, QUERY_USER_SELECT);

class GroupStats extends Module {
    private $owner;
    public $config;
    public $group_data;
    private $group_formed = false;
    public $user_count = 0;
    public $homework_count = 0;
    public $editors_count = 0;
    public $date;
    function __construct($owner, $config) {
        global $mysql;
        $this->owner = $owner;
        $this->config = $config;
        $user = User::loadFromSession();
        $this->config_load_default();
        $this->active = false;

        $this->group_data = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $user->group));
        if ($this->group_data) {
            $this->group_formed = true;
            $this->user_count = count(assoc_to_arr($mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("group" => $this->group_data['name']))));
            $hw = $mysql->exec(QUERY_HOMEWORK_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $this->group_data['id']));
            $this->homework_count = ($hw) ? count(assoc_to_arr($hw)) : 0;
            $editors = $mysql->exec(QUERY_CONTRIBUTOR_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $this->group_data['id']));
            $this->editors_count = ($editors) ? count(assoc_to_arr($editors)) : 0;
        }
    }

    function group_exists() {
        return $this->group_formed;
    }
}