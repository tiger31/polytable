<?php
namespace Interaction\APICall;
use Interaction\APICall;
use Security\Shield;
use User\User;

class api_check extends APICall {
    function __construct($config) {
        $this->name = "check";
        $this->fields = json_decode('[{"name":"field","isset":true},{"name":"value","isset":true}]', true);
        $this->bit_mask = 0;
        $this->user_needed = null;
        $this->force_csrf_check = false;
        $this->method = "GET";
        $this->input = $_GET;

        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        switch ($this->input['field']) {
            case "login":
                $result = $mysql->login_free($_GET['value']);
                $this->response->response($result);
                break;
            case "email":
                $this->response->response($mysql->email_free($_GET['value']) || (User::$user && User::$user->email == $_GET['value']));
                break;
            default:
                $this->response->error(400, array("info" => "Ajax is not provided"))->response();
                break;
        }
    }
}