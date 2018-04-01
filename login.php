<?php
    error_reporting(-1);
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    include_once $local_modules_path . "/Connect.php";
    include_once $local_modules_path . "/classes/User.php";
    global $mysql;
    $mysql->set_active(QUERY_USER_SELECT, QUERY_USER_UPDATE);
    session_start();

    include_once $local_modules_path . "/Security.php";

    if (isset($_POST['login']) and isset($_POST['password']) and !session_check()) {
        $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_POST["login"]));
        if ($user && $user['active'] == 1) {
            if (password_verify(escape_string($_POST['password']), $user['password_hash'])) {
                $_SESSION['user'] = new User($user, $mysql);
                $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
                $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, array("ip" => $_SERVER["REMOTE_ADDR"], "id" => $user["id"]));
            } else {
                $_SESSION['user'] = null;
                session_destroy();
            }

        }
    }
    header('Location: ' . ((isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "groups.php")));
    die();