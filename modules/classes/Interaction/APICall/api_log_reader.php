<?php

namespace Interaction\APICall;


use Configuration\Logger;
use Interaction\APICall;

class api_log_reader extends APICall
{
    function __construct($config)
    {
        $this->name = "log_reader";
        $this->fields = json_decode('[{"name":"lines","isset":true}]', true);
        $this->method = "GET";
        $this->input = $_GET;
        parent::__construct($config);
    }

    function handle()
    {
        try {
            $this->response->response(true, array("data" => Logger::read($this->input["lines"])));
        } catch (\Exception $e) {
            $this->response->response();
        }
    }
}