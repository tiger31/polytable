<?php
namespace Interaction\APICall;

use Files\Files;
use Interaction\APICall;

class api_upload extends APICall {
    function __construct($config) {
        $this->name = "upload";
        $this->fields = array();
        $this->bit_mask = 16385;
        $this->user_needed = true;
        $this->force_csrf_check = false;
        $this->method = "POST";
        $this->input = $_POST;

        parent::__construct($config);
    }
    function handle() {
        if ($_FILES == null)
            $this->response->error(401, array("info" => "File haven't received"))->response();
        Files::store_file();
    }
}