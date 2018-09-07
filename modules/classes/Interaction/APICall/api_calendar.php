<?php

namespace Interaction\APICall;

use Interaction\APICall;
use Cache\Timetable;

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
            $static = Timetable::fine_format(Timetable::static_from_database($group['id']));
            $db_dynamic = Timetable::dynamic_from_database($group['id']);
            $db_homework = $mysql(QUERY_HOMEWORK_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $group['id']));
            if ($db_homework) {
                if (is_assoc($db_homework)) $db_homework = [$db_homework];
            } else $db_homework = [];
            $dynamic = [];
            $homework = [];
            foreach ($db_dynamic as $lesson)
                $dynamic[$lesson['day']][$lesson['lesson']] = $lesson;
            foreach ($db_homework as $lesson) {
                $json = json_decode($lesson['text'], true);
                $files = $json['files'];
                $files_arr = array();
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
                $homework[$lesson['date']][$lesson['lesson']]['files'] = (count($files_arr) > 0) ? $files_arr : null;
                $homework[$lesson['date']][$lesson['lesson']]['text'] = ($json['text'] == "") ? null : $json['text'];
            }
            $this->response->response(true, array("data" => array(
                "static_start" => "2018-09-03",
                "static_end" => "2018-12-31",
                "timetable_start" => "2018-09-03",
                "timetable_end" => "2019-01-31",
                "static" => $static,
                "dynamic" => $dynamic,
                "homework" => $homework,
                "cache_last" => $group['cache_last'],
                "static_version" => $group['static_changed']
                ))
            );

        }
        $this->response->response(false);
    }
}