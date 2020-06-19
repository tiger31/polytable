<?php

namespace User\Auth;

use User\Auth;

class SSeedAuth extends Auth {

    public function __construct() {
        global $mysql;
        $hash = $_COOKIE['sseed'];
        $user_data = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("sseed" => $hash));
        if ($user_data) {
            $this->auth($user_data['login'], false, false);
        }
    }
}