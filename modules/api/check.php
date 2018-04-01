<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class check extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        $this->pre_check();
        switch ($_GET['field']) {
            case "login":
                $result = $mysql->login_free($_GET['value']);
                AjaxResponse::create()->response($result);
                break;
            case "email":
                AjaxResponse::create()->response($mysql->email_free($_GET['value']) || (session_check(false) && User::loadFromSession()->email == $_GET['value']));
                break;
            default:
                AjaxResponse::create()->error(400, array("info" => "Ajax is not provided"))->response();
                break;
        }
    }
}