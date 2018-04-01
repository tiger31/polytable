<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class register extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql, $default_redirect;
        $this->pre_check();
        $valid = false;
        $login_free = $mysql->login_free($_GET['login']);
        if (!$login_free) {
            AjaxResponse::create()
                ->error(400, array("info" => "Login is taken", "affected_row" => "login", "state" => "taken"))
                ->response();
        }
        if (!($mysql->email_free($_GET['value']) || (session_check(false) && User::loadFromSession()->email == $_GET['value'])))
            AjaxResponse::create()
                ->error(400, array("info" => "Email is taken", "affected_row" => "email", "state" => "taken"))
                ->response();
        $data = array(
            "login" => $_GET["login"],
            "email" => $_GET["email"],
            "group" => $_GET["group"],
            "password_hash" => password_hash($_GET['password'], PASSWORD_BCRYPT),
        );
        if ($mysql->exec(QUERY_USER_INSERT, RETURN_IGNORE, $data)) {
            include_once $_SERVER['DOCUMENT_ROOT'] . "/mail.php";
            $valid = accept_request($_GET['login']);
        } else {
            $valid = false;
        }
        if ($valid) {
            $arr = array();
            $arr['title'] = "На указанный вами e-mail было отправлено письмо с подтверждением";
            $arr['group'] = $data['group'];
            $mysql->set_active(QUERY_EMAIL_SELECT);
            $email = substr($data["email"],strpos($data["email"],"@") + 1);
            $data = $mysql->exec(QUERY_EMAIL_SELECT, RETURN_FALSE_ON_EMPTY, array("url" => $email));
            if ($data) {
                $arr['url'] = $data['url'];
                $arr['name'] = $data['name'];
            }
            $arr['default'] = "/groups.php?id=" . $_GET["group"];
            AjaxResponse::create()->response(true, $arr);
        } else {
            AjaxResponse::create()->response(false);
        }
    }
}