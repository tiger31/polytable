<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";

include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/AjaxResponse.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Security.php";




abstract class APICall {
    protected $config;
    protected $user;
    function __construct($config) {
        global $user;
        $this->config = $config;
        $this->user = $user;
    }
    function pre_check() {
        global $mysql;
        $METHOD = array();
        if ($this->config["method"] == "GET")
            $METHOD =& $_GET;
        else
            $METHOD =& $_POST;
        if ($this->config["user"] && $this->config["user"]["logged"] !== session_check(false))
            AjaxResponse::create()->error(401, array("info" => "Unauthorized or forbidden"))->response();
        if ($this->config["rights"] && $this->config["right"]["value"] < $this->user->getPrivilegesLevel())
            AjaxResponse::create()->error(403, array("info" => "Forbidden1"))->response();
        if ($this->config["group"] && (($this->config["group"] == "head" && !$this->user->is_head) || ($this->config["group"] == "editor" && !$this->user->group_editor())))
            AjaxResponse::create()->error(403, array("info" => "Forbidden2"))->response();
        if ($this->config["isset"]) {
            foreach ($this->config["fields"] as $field)
                if (!isset($METHOD[$field]))
                    AjaxResponse::create()->error(400, array("info" => "Not enough data"))->response();
        }
        if ($this->config["regex"])
            check_fields($this->config['fields'], $this->config["regex"]["allow_empty"]);
        if ($this->config["mysql"])
            $mysql->set_active(...$this->config["mysql"]);
    }
    abstract function handle();
}
