<?php

namespace User;

use Security\CSRF;
use Security\Shield;

abstract class Auth {

    private $user = null;
    private $success = false;

    protected function auth($login, $redirect=true, $modify_cookie = true, $cookie = 3600) {
        global $mysql;
        $user_data = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $login));
        if ($user_data && $user_data['active'] == 1) {

            if (!$user_data['session_hash']) {
                $hash = md5(Shield::rnd_str(16)) . md5(Shield::rnd_str(32));
                $mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array(
                    "login" => $user_data['login'],
                    "sseed" => $hash
                ));
                $user_data['session_hash'] = $hash;
            }

            $this->user = new User($user_data);
            $this->success = true;
            $mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array("ip" => $_SERVER["REMOTE_ADDR"], "id" => $user_data["id"]));
            User::$user = $this->user;
            $_SESSION['X-USER-ID'] = $this->user->id;
            $_SESSION['X-USER-AGENT'] = $_SERVER['HTTP_USER_AGENT'];
            if ($modify_cookie)
                setcookie("sseed", $user_data['session_hash'], time() + $cookie, "; SameSite=Strict;", "", false);
            CSRF::set_csrf_token();
            if ($redirect) static::redirect();
        }
    }

    static function close($redirect=true) {
        User::$user = null;
        unset($_SESSION['X-USER-ID'], $_SESSION['X-USER-AGENT']);
        setcookie("sseed", "", 1, "; SameSite=Strict;", "", false);
        CSRF::unset();
        if ($redirect) static::redirect();
    }

    static function redirect() {
        header('Location: ' . ((isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "index.php")));
        die();
    }

}