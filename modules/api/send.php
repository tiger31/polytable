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
            $json = json_decode($_GET['text'], true);
            $files = array();
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
                            array_push($new_set, $data['name']);
                            array_push($files, array(
                                "name" => $data['name'],
                                "original" => $data['original_name'],
                                "showable" => $data['showable'],
                                "size" => $data["size"]
                            ));
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
            AjaxResponse::create()->response($result, array("text" => $json['text'], "files" => $files));
        } else {
            AjaxResponse::create()->error(400, array("info" => "Data is invalid"))->response();
        }
    }
}