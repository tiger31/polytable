<?php
error_reporting(-1);
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Connect.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/AjaxResponse.php";
global $mysql;
$mysql->set_active(QUERY_GROUP_INSERT, QUERY_GROUP_CHECK, QUERY_USER_CHECK, QUERY_CONFIRM_INSERT, QUERY_USER_SELECT);

if (session_status() !== 2) session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Security.php";
function accept_request($login)
{
    global $mysql;
    if (isset($login)) {
        //Prepared mysql query for selecting single "head request";
        $data = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $login));
        if (!$data)
            AjaxResponse::create()
                ->error(400, array("info" => "Login is invalid", "affected_row" => "login", "state" => "not found"))
                ->response();

        $hash = md5(generateRandomString(32));
        $link = $_SERVER['HTTP_HOST'] . "/confirm.php?login=" . $login . "&hash=" . $hash;

        $now = new DateTime("now");
        $now->modify("+6 hours");

        $mysql->exec(QUERY_CONFIRM_INSERT, RETURN_IGNORE,
            array(
                "value" => $hash,
                "login" => $login,
                "for" => "ACCOUNT",
                "lifetime" => $now->format("Y-m-d H:i:s")
            ));


        //письмо на email для подтверждения
        $to = $data["email"]; // mail.ru плохо разбирает заголовок
        $subject = (isset($group_id)) ? "Подтверждения заявки группы " . $data["group"] : "Подтверждение регистрации";
        $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: polytable.ru <no-reply@polytable.ru>\r\n";
        $headers .= "Replay-To: Support <support@polytable.ru>\r\n";
        $headers .= "X - Priority: 1 (Highest)\r\n";
        $headers .= "X - MSMail - Priority: High\r\n";
        $headers .= "Importance: High";

        $message = file_get_contents("templates / mail . html");
        $message = str_replace("{
            LINK}", $link, $message);
        $message = str_replace("{
            HOST}", $_SERVER['HTTP_HOST'], $message);
        mail($to, $subject, $message, $headers);
        return true;
    }
    AjaxResponse::create()->error(400, array("info" => "Not enough data"))->response();
    return false;
}

