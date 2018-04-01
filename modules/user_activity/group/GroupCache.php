<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Connect.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/Module.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User.php";
global $mysql;
$mysql->set_active(QUERY_GROUP_SELECT);

class GroupCache extends Module {
    private $owner;
    public $config;
    public $user;
    public $group;
    public $cached_last;
    public $cached_until;
    function __construct($owner, $config) {
        global $mysql;
        $this->owner = $owner;
        $this->config = $config;
        $this->config_load_default();
        $this->user = User::loadFromSession();
        $this->active = true;
        $this->group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $this->user->group));
        $cache_date = ($this->group['cache_last'] != null) ? new DateTime($this->group['cache_last']) : null;
        $this->cached_last = ($cache_date !== null) ? $cache_date->format("d/m H:i") : "никогда";
        $cache_until = ($this->group['cache_until'] != null) ? new DateTime($this->group['cache_until']) : null;
        //TODO negative date
        $this->cached_until = ($cache_until !== null) ? $cache_until->format("d/m/Y") . " (осталось " . $cache_until->diff(new DateTime("now"))->format("%a")." дней)"
            : "кэш отсутствует";
    }
}