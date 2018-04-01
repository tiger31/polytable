<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class editor extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        $this->pre_check();
        $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_GET['login']));
        switch ($_GET['event']) {
            case "add":
                if (!$user || $user['group'] != $this->user->group) {
                    AjaxResponse::create()->response(false, array("info" => "User doesn't exist or has different group"));
                } else {
                    if ($mysql->exec(QUERY_CONTRIBUTOR_CHECK, RETURN_TRUE_ON_EMPTY, array("gr_id" => $this->user->group_id, "id" => $user["id"])))
                        AjaxResponse::create()->response($mysql->exec(QUERY_CONTRIBUTOR_INSERT, RETURN_IGNORE,
                            array("user_id" => $user["id"], "group_id" => $this->user->group_id)), array("info" => array("id" => $user["id"], "login" => $user["login"])));
                    else AjaxResponse::create()->response();
                }
            break;
            case "remove":
                if (!$user || $user['group'] != $this->user->group) {
                    AjaxResponse::create()->response(false, array("info" => "User doesn't exist or has different group"));
                } else {
                    AjaxResponse::create()->response($mysql->exec(QUERY_CONTRIBUTOR_DELETE, RETURN_IGNORE,
                        array("user_id" => $user["id"], "group_id" => $this->user->group_id)));
                }
                break;
            default:
                AjaxResponse::create()->error(400, array("info" => "No such method provided"))->response();
                break;
        }
    }
}