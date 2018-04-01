<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class get extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }
    function handle() {
        global $mysql, $user;
        $this->pre_check();
        if (DateTime::createFromFormat("Y-m-d", $_GET['date']) === false)
            AjaxResponse::create()->error(400, array("info" => "Data is invalid"))->response();
        $data = array(
            "group_id" => $user->group_id,
            "date" => $_GET['date'],
            "lesson" => $_GET['lesson']
        );
        $result = $mysql->exec(QUERY_HOMEWORK_SELECT, RETURN_FALSE_ON_EMPTY, $data);
        if (!$result) {
            AjaxResponse::create()->response(false);
        } else {
            AjaxResponse::create()->response(true, array("text" => json_decode($result['text'])));
        }
    }
}