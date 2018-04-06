<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    include_once $local_modules_path . "/Connect.php";
    global $mysql;

    $mysql->set_active(
        QUERY_USER_CHECK,
        QUERY_USER_UPDATE,
        QUERY_GROUP_CHECK,
        QUERY_HOMEWORK_INSERT,
        QUERY_HOMEWORK_CHECK,
        QUERY_HOMEWORK_SELECT,
        QUERY_HOMEWORK_UPDATE
    );

    include_once $local_modules_path . "/classes/User.php";
    include_once $local_modules_path . "/classes/AjaxResponse.php";
    include_once $local_modules_path . "/Security.php";

    session_start();
    $user = User::loadFromSession();

    header("Content-Type: application/json");

    //КАСТЫЛИ!!!! НАПИШИ НОРМАЛЬНУЮ СИСТЕМУ МОДУЛЕЙ
    if (!isset($_GET['action']) && !isset($_POST['action'])) {
        AjaxResponse::create()->error(400, array("info" => "Request sent without action"))->response();
    } else {
        if (isset($_GET['action']))
        switch ($_GET['action']) {
            case "check":
                get_api_handler($_GET['action'])->handle();
                break;
            case "send" :
                get_api_handler($_GET['action'])->handle();
                break;
            case "register":
                get_api_handler($_GET['action'])->handle();
                break;
            case "get":
                get_api_handler($_GET['action'])->handle();
                break;
            case "editor":
                get_api_handler($_GET['action'])->handle();
                break;
            case "cache":
                get_api_handler($_GET['action'])->handle();
                break;
            case "update":
                get_api_handler($_GET['action'])->handle();
                break;
            case "calendar":
                get_api_handler($_GET['action'])->handle();
                break;
            default:
                AjaxResponse::create()->error(400, array("info" => "No such method provided"))->response();
                break;
        }
        if (isset($_POST['action']))
        switch ($_POST['action']) {
            case "upload" :
                get_api_handler($_POST['action'])->handle();
                break;
            default:
                AjaxResponse::create()->error(400, array("info" => "No such method provided"))->response();
                break;

        }
    }

    function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
    function check_fields($fields, $empty = false) {
        $errors = new AjaxResponse();
        foreach ($fields as $field) {
            if (!isset($_GET[$field]) or (!preg_match(REGEX[$field], $_GET[$field]) and ($_GET[$field] == "" and !$empty))) {
               $errors->error(400, array("info" => "$field value not allowed", "affected_row" => $field, "state" => "invalid"));
            }
        }
        if (isset($_GET['email']) and  strlen($_GET['email']) > 32)
            $errors->error(400, array("info" => "E-mail value not allowed", "affected_row" => "email", "state" => "invalid"));
        if (isset($_GET['password'], $_GET['password_hash']) && $_GET['password'] !== $_GET['password_hash'])
            $errors->error(400, array("info" => "Passwords are different", "affected_row" => "password", "state" => "compare"));
        return ($errors->is_error()) ? $errors : true;
    }
    function get_api_handler($action) {
        if (api_handler_exists($action))
            return new $action(API_CALLS[$action]);
        return false;
    }
    function api_handler_exists($query) {
        $query_class_file = $_SERVER["DOCUMENT_ROOT"] . "/modules/api/" . $query . ".php";
        if (file_exists($query_class_file)) {
            //Ignore warning because of autoload function
            require_once $query_class_file;
            return true;
        }
        return false;
    }
