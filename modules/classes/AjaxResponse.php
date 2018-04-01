<?php

class AjaxResponse {

    private $errors;
    const RESPONSE = array(
        true => array("response" => true),
        false => array("response" => false),
        400 => array("error" => "Bad Request", "code" => 400),
        401 => array("error" => "Unauthorized", "code" => 401),
        403 => array("error" => "Forbidden", "code" => 403),
        405 => array("error" => "Method Not Allowed", "code" => 405),
        500 => array("error" => "Internal Server Error", "code" => 500)
    );

    function __construct() {
        $this->errors = array();
    }

    static function create() {
        return new AjaxResponse();
    }

    function is_error() {
        return count($this->errors) !== 0;
    }

    function error($code, $data = null) {
        array_push($this->errors, ($data) ? array_merge(static::RESPONSE[$code], $data) : static::RESPONSE[$code]);
        return $this;
    }

    function response($result = null, $data = null) {
        if ($result === null) {
            die(json_encode(($this->is_error()) ? array("errors" => $this->errors) : static::RESPONSE[false], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode(($this->is_error()) ? array("errors" => $this->errors) : ($data) ? array_merge(static::RESPONSE[(bool)$result], $data) : static::RESPONSE[(bool)$result], JSON_UNESCAPED_UNICODE));
        }
    }


}