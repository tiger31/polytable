<?php

namespace Interaction\APICall;

use Interaction\APICall;

class api_calendar extends APICall {
    function __construct($config) {
        $this->name = "calendar";
        $this->fields = json_decode('[{"name":"group","isset":true,"regex":true},{"name":"date","isset":false},{"name":"lesson","isset":false}]', true);
        $this->bit_mask = 0;
        $this->user_needed = null;
        $this->force_csrf_check = false;
        $this->method = "GET";
        $this->input = $_GET;

        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        if (!isset($_GET['day'])) {
            $group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $_GET['group']));
            if (!$group)
                $this->response->error(400, array("info" => "Data is invalid", "affected_row" => "group", "state" => "invalid"))->response();
            $data = $mysql->exec(QUERY_CALENDAR_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $group['id']));
            for ($i = 0; $i < count($data); $i++) {
                $json = json_decode($data[$i]['text'], true);
                $files = $json['files'];
                $files_arr = array();
                $mysql->set_active(QUERY_FILE_SELECT);
                for ($j = 0; $j < count($files); $j++) {
                    $file = $mysql(QUERY_FILE_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $files[$j]));
                    if ($file) {
                        array_push($files_arr, array(
                            "name" => $file["name"],
                            "original" => $file["original_name"],
                            "showable" => $file["showable"],
                            "size" => $file["size"]
                        ));
                    }
                }
                $data[$i]['files'] = (count($files_arr) > 0) ? $files_arr : null;
                $data[$i]['text'] = ($json['text'] == "") ? null : $json['text'];
                $data[$i]['teachers'] = json_decode($data[$i]['teachers'], true);
                $data[$i]['places'] = json_decode($data[$i]['places'], true);
            }
            $result = array();
            foreach ($data as $lesson) {
                $result[$lesson["day"]][] = $lesson;
            }
            $this->response->response(true, array("data" => array(
                "days" => $result,
                "cache_last" => $group['cache_last'],
            )));
        }
        $this->response->response(false);
    }
}