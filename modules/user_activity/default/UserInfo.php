<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/Module.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User.php";

class UserInfo extends Module {
    private $owner;
    public $config;
    public $user;
    function __construct($owner, $config) {
        $this->owner = $owner;
        $this->config = $config;
        $this->active = true;
        $this->user = User::loadFromSession();
        $this->config_load_default();
    }
}