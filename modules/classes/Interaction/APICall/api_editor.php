<?php
namespace Interaction\APICall;
use Interaction\APICall;

class api_editor extends APICall {
    function __construct($config) {
        $this->name = "editor";
        $this->fields = json_decode('[{"name":"group","isset":true,"regex":true},{"name":"login","isset":true},{"name":"event","isset":true}]"', true);
        $this->bit_mask = 1536;
        $this->user_needed = true;
        $this->force_csrf_check = false;
        $this->method = "POST";
        $this->input = $_POST;

        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_GET['login']));
        switch ($_GET['event']) {
            case "add":
                if (!$user || $user['group'] != $this->user->group) {
                    $this->response->response(false, array("info" => "User doesn't exist or has different group"));
                } else {
                    if ($mysql->exec(QUERY_CONTRIBUTOR_CHECK, RETURN_TRUE_ON_EMPTY, array("gr_id" => $this->user->group_id, "id" => $user["id"])))
                        $this->response->response($mysql->exec(QUERY_CONTRIBUTOR_INSERT, RETURN_IGNORE,
                            array("user_id" => $user["id"], "group_id" => $this->user->group_id)), array("info" => array("id" => $user["id"], "login" => $user["login"])));
                    else $this->response->response();
                }
            break;
            case "remove":
                if (!$user || $user['group'] != $this->user->group) {
                    $this->response->response(false, array("info" => "User doesn't exist or has different group"));
                } else {
                    $this->response->response($mysql->exec(QUERY_CONTRIBUTOR_DELETE, RETURN_IGNORE,
                        array("user_id" => $user["id"], "group_id" => $this->user->group_id)));
                }
                break;
            default:
                $this->response->error(400, array("info" => "No such method provided"))->response();
                break;
        }
    }
}