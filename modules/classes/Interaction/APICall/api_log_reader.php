<?php

namespace Interaction\APICall;


use Interaction\APICall;

class api_log_reader extends APICall
{
    function __construct($config)
    {
        $this->name = "log_reader";
        $this->fields = array();
        $this->bit_mask = 131072;
        $this->user_needed = true;
        $this->method = "GET";
        $this->input = $_GET;
        parent::__construct($config);
    }

    function handle()
    {
        try {
            $this->response->response(true, array("data" => \Configuration\Logger::read()));
        } catch (\Exception $e) {

        }
    }
}