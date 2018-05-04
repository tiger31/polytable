<?php declare(strict_types=1);

namespace Interaction;

use Security\Shield;
use Security\CSRF;
use User\User;

abstract class APICall {
    protected $user;
    protected $input;

    protected $name;
    protected $fields;
    protected $bit_mask;
    protected $user_needed;
    protected $force_csrf_check;
    protected $response;
    protected $method;
    function __construct($config) {
        $this->user = User::$user;
        $this->response = new Response();
        $this->pre_check();
    }

    private function pre_check() {
        //User checks
        if ($this->user_needed !== null) {
            if ($this->user_needed) {
                if (User::$user) {
                    $headers = getallheaders();
                    if ($this->method === "POST" || $this->force_csrf_check)
                        if (!$headers || !isset($headers['X-CSRF-TOKEN']) || !isset($_COOKIE['X-CSRF-TOKEN']) || !CSRF::verify_csrf_token($headers['X-CSRF-TOKEN']))
                            $this->response->error(403, array("info" => "Forbidden"))->response();
                    //Rights check
                    if ($this->bit_mask > 0) {
                        if (!$this->user->have_access($this->bit_mask)) {
                            $this->response->error(403, array("info" => "Permission denied"))->response();
                        }
                    }
                } else {
                    $this->response->error(403, array("info" => "You should be logged in"))->response();
                }
            } else {
                if (User::$user) {
                    $this->response->error(403, array("info" => "You are already logged in"))->response();
                }
            }
        }
        //Fields check
        foreach($this->fields as $field) {
            if ($field['isset'] === true) {
                if (isset($this->input[$field['name']])) {
                    if ($field['regex'] === true) {
                        if (!preg_match(REGEX[$field['name']], $this->input[$field['name']]))
                            $this->response->error(400, array(
                                    "info" => "Data invalid",
                                    "affected_row" => $field['name'],
                                    "state" => "invalid")
                            )->response();
                    }
                } else {
                    $this->response->error(400, array(
                        "info" => "Data incomplete",
                        "affected_row" => $field['name'],
                        "state" => "empty")
                    )->response();
                }
            }
        }

    }
    abstract function handle();

    static function get_handler($name) {
        $file = $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/" . str_replace('\\', "/", __NAMESPACE__) . "/APICall/" . $name . ".php";
        require_once $file;
    }

}
