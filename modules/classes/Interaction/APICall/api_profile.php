<?php


namespace Interaction\APICall;

use Interaction\APICall;
use DataView\Profile;

class api_profile extends APICall {
    function __construct($config) {
        $this->name = "profile";
        $this->fields = array();
        $this->bit_mask = 0;
        $this->user_needed = true;
        $this->force_csrf_check = true;
        $this->method = "GET";
        $this->input = $_GET;
        parent::__construct($config);
    }

    function handle() {
        try {
            $profile = new Profile();
            $this->response->response("true", array("data" => $profile->get_data()));
        } catch (\Exception $e) {
            $this->response->response();
        }
    }
}