<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Connect.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/Module.php";
global $mysql;
$mysql->set_active(QUERY_GROUP_SELECT, QUERY_USER_SELECT, QUERY_CONTRIBUTOR_SELECT);

class GroupHead extends Module {
    private $owner;
    public $config;
    public $user;
    public $head_exists;
    public $head;
    public $editors = array();
    function __construct($owner, $config) {
        global $mysql;
        $this->owner = $owner;
        $this->config = $config;
        $this->user = User::loadFromSession();
        $this->config_load_default();
        $this->active = true;


        $group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $this->user->group));
        if ($group['header_login'] != null) {
            $this->head_exists = true;
            $this->head = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $group['header_login']));
            $editors = $mysql->exec(QUERY_CONTRIBUTOR_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $group["id"]));
            if ($editors) {
                $editors = is_assoc($editors) ? array($editors) : $editors;
                foreach ($editors as $editor) {
                    if ($editor)
                        array_push($this->editors, $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("id" => $editor['user_id'])));
                }
            }
        } else {
            $this->head_exists = false;
        }
    }
}