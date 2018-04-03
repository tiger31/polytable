<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";
class calendar extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        $this->pre_check();
        if (!isset($_GET['group']))
            AjaxResponse::create()->error(400, array("info" => "Empty request"))->response();
        if (!isset($_GET['day'])) {
            $group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $_GET['group']));
            if (!$group)
                AjaxResponse::create()->error(400, array("info" => "Data is invalid", "affected_row" => "group", "state" => "invalid"))->response();
            $data = $mysql->exec(QUERY_CALENDAR_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $group['id']));
            for ($i = 0; $i < count($data); $i++) {
                if (isset($data[$i]['files']))
                    $data[$i]['files'] = json_decode($data[$i]['files'], true);
                $data[$i]['teachers'] = json_decode($data[$i]['teachers'], true);
                $data[$i]['places'] = json_decode($data[$i]['places'], true);
            }
            $result = array();
            foreach ($data as $lesson) {
                $result[$lesson["day"]][] = $lesson;
            }
            AjaxResponse::create()->response(true, array("data" => array(
                "days" => $result,
                "cache_last" => $group['cache_last'],
            )));
        }
        AjaxResponse::create()->response(false);
    }
}