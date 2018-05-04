<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";

    use User\User;
    use Interaction\Response;
    use Interaction\APICall;

    $user = User::$user;

    $headers = getallheaders();

    if (!isset($headers['Host'], $headers['X-Requested-With']) || $headers['Host'] !== $_SERVER['HTTP_HOST']
        || $headers['X-Requested-With'] !== "XMLHttpRequest") {
        Response::create()->error(403, array("info" => "Probably malformed request"))();
    }

    header("Content-Type: application/json");

    if (!isset($_GET['action']) && !isset($_POST['action'])) {
        Response::create()->error(400, array("info" => "Request sent without action"))();
    } else {
        $action = (isset($_GET['action'])) ? $_GET['action'] : $_POST['action'];
        if (class_exists_e("Interaction\APICall\api_$action")) {
            $handler = get_api_handler($action);
            $handler->handle();
        } else {
            Response::create()->error(405, array("info" => "No handler for this action"))();
        }
    }

    function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
    function check_fields($fields, $empty = false) {
        $errors = new Response();
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
        global $api_calls;
        //TODO Handlers id different class
        $handler = "Interaction\APICall\api_$action";
        return new $handler($api_calls[$action]);
    }
