<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class upload extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }
    function handle() {
        $this->pre_check();
        if ($_FILES == null)
            AjaxResponse::create()->error(4001, array("info" => "File haven't received"))->response();
        include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/FileUpload.php";
        store_file();
    }
}