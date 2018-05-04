<?php

namespace User\Auth;

use Security\Shield;
use User\Auth;
use User\User;

class Auth_pass extends Auth {

    public function __construct() {
        global $mysql;
        if (isset($_POST['login'], $_POST['password']) && !User::$user) {
            $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_POST["login"]));
            if ($user) {
                if (password_verify(Shield::escape_str($_POST['password']), $user['password_hash'])) {
                    $lifetime = (isset($_POST['remember'])) ? 86400 * 30 : 86400;
                    $this->auth($user['login'], true, true, $lifetime);
                    die();
                }
            }
        }
        static::redirect();
    }
}