<?php
namespace Interaction\APICall;
use Interaction\APICall;

class api_homework extends APICall {
    function __construct($config) {
        $this->name = "homework";
        $this->fields = json_decode('[{"name":"group","isset":true,"regex":true},{"name":"date","isset":true},{"name":"lesson","isset":true},{"name":"text","isset":true}]', true);
        $this->bit_mask = 1;
        $this->user_needed = true;
        $this->force_csrf_check = false;
        $this->method = "POST";
        $this->input = $_POST;

        parent::__construct($config);
    }

    function handle() {
        global $mysql, $user;
        if (isJSON($this->input['text']) && \DateTime::createFromFormat("Y-m-d", $this->input['date']) !== false) {
            $json = json_decode($this->input['text'], true);
            if (mb_strlen($json['text'], "utf-8") > 140)
                $this->response
                    ->error(400, array("info" => "Text is to large", "affected_row" => "text", "state" => "invalid"))
                    ->response();
            $files = array();
            if ($json['files'] !== null && count($json['files']) > 0) {
                $name_list = $json['files'];
                $date = new \DateTime($this->input['date']);
                $date->modify("+2 month");
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
                "date" => $this->input['date'],
                "lesson" => $this->input['lesson'],
                "text" => json_encode($json, JSON_UNESCAPED_UNICODE)
            );
            $result = $mysql->exec(QUERY_HOMEWORK_INSERT, RETURN_IGNORE, $rows);
            $this->response->response($result, array("text" => $json['text'], "files" => $files));
        } else {
            $this->response->error(400, array("info" => "Data is invalid"))->response();
        }
    }
}