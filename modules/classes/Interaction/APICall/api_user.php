<?php
/**
 * Created by IntelliJ IDEA.
 * User: Fluttershy
 * Date: 02.05.2018
 * Time: 4:35
 */

namespace Interaction\APICall;

use Interaction\APICall;
use Security\Shield;

class api_user extends APICall{
    function __construct($config) {
        $this->name = "user";
        $this->fields = [
            array(
                "name" => "type",
                "isset" => true
            )
        ];
        $this->bit_mask = 0;
        $this->user_needed = true;
        $this->force_csrf_check = false;
        $this->method = "POST";
        $this->input = $_POST;

        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        switch ($this->input['type']) {
            case "change":
                $this->response->response($mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array(
                    "login" => $this->user->login,
                    "number" => $this->input['number']
                )));
                break;
            case "password":
                if (password_verify(Shield::escape_str($this->input['password']), $this->user->password_hash)) {
                    $new_password = password_hash($this->input['new_password'], PASSWORD_BCRYPT);
                    $hash = md5(Shield::rnd_str(16)) . md5(Shield::rnd_str(32));
                    $mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array(
                        "login" => $this->user->login,
                        "sseed" => $hash
                    ));
                    setcookie("sseed", $hash, time() + 86000, "; SameSite=Strict;", "", false);
                    $this->response->response($mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array(
                        "login" => $this->user->login,
                        "pass" => $new_password
                    )));
                } else {
                    $this->response->error(403, array("info" => "Wrong password"))->response();
                }
                break;
        }
    }
}