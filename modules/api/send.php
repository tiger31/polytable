<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class send extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql, $user;
        $this->pre_check();
        if (isJSON($_GET['text']) && DateTime::createFromFormat("Y-m-d", $_GET['date']) !== false) {
            $is_new = true;
            if ($mysql->exec(QUERY_HOMEWORK_CHECK, RETURN_FALSE_ON_EMPTY, array(
                    "group_id" => $user->group_id,
                    "date" => $_GET['date'],
                    "lesson" => (int)$_GET['lesson'])
                )
            ) {
                $is_new = false;
            }
            $json = json_decode($_GET['text'], true);
            if ($json['files'] !== null && count($json['files']) > 0) {
                $name_list = $json['files'];
                $date = new DateTime($_GET['date']);
                $date->modify("+1 month");

                if ($name_list) {
                    $new_set = array();
                    foreach (array_slice($name_list, 0, 5) as $file) {
                        $data = $mysql->exec(QUERY_FILE_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $file));
                        if ($data) {
                            $mysql->exec(QUERY_FILE_UPLOAD, RETURN_IGNORE, array("stored_untill" => $date->format("Y-m-d"), "name" => $data['name']));
                            $file_upload_date = new DateTime($data['added']);
                            $file_data = array(
                                "showable" => $data["showable"],
                                "name" => $data["name"],
                                "original" => $data["original_name"],
                                "date" => $file_upload_date->format("Y-m-d")
                            );
                            array_push($new_set, $file_data);
                        }
                    }
                    $json['files'] = $new_set;
                }
            }
            $rows = array(
                "group_id" => $user->group_id,
                "sender_id" => $user->id,
                "date" => $_GET['date'],
                "lesson" => $_GET['lesson'],
                "text" => json_encode($json, JSON_UNESCAPED_UNICODE)
            );
            $result = $mysql->exec(QUERY_HOMEWORK_INSERT, RETURN_IGNORE, $rows);
            AjaxResponse::create()->response($result, array("text" => $json));
        } else {
            AjaxResponse::create()->error(400, array("info" => "Data is invalid"))->response();
        }
    }
}