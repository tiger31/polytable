<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class update extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        $this->pre_check();
        if ($_GET['email'] == "")
            AjaxResponse::create()
                ->error(400, array("info" => "Email is empty", "affected_row" => "email", "state" => "empty"))
                ->response();
        if (!($mysql->email_free($_GET['email']) || (session_check(false) && User::loadFromSession()->email == $_GET['email'])))
            AjaxResponse::create()
                ->error(400, array("info" => "Email is taken", "affected_row" => "email", "state" => "taken"))
                ->response();
        //TODO send letter if email is changed
        $data = array();
        if ($this->user->verified) {
            $data = array(
                "login" => $this->user->login,
                "email" => $_GET["email"],
                "number" => ($_GET['number'] !== "") ? $_GET["number"] : $this->user->number,
                "vk_link" => ($_GET['vk_link'] !== "") ? $_GET["vk_link"] : $this->user->vk_link
            );
        } else {
            $data = array(
                "login" => $this->user->login,
                "email" => $_GET["email"],
                "number" => ($_GET['number'] !== "") ? $_GET["number"] : null,
                "vk_link" => ($_GET['vk_link'] !== "") ? $_GET["vk_link"] : null
            );
        }
        $result = $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, $data);
        if ($result) {
            $this->user->number = $data['number'];
            $this->user->vk_link = $data["vk_link"];
            if (!$this->user->verified && $data['number'] !== null && $data["vk_link"] !== null) {
                $this->user->verified = true;
                $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, array("id" => $this->user->id));
            }

        }
        AjaxResponse::create()->response($result);
    }
}