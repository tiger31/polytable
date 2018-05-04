<?php

namespace Security;
use User\Auth\SSeedAuth;
use User\User;

class Session {

    private $reserved = array(
        "X-CSRF-TOKEN" => "Security\\CSRF",
        "X-USER-ID" => "User\\Auth",
        "X-USER-AGENT" => "User\\Auth"
    );

    public function __construct() {
        global $mysql;
        if (session_status() !== 2) session_start();
        if (isset($_COOKIE['sseed'])) {
            if (isset($_SESSION["X-USER-ID"])) {
                $user_data = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("id" => $_SESSION["X-USER-ID"]));

                if (!isset($_SESSION["X-USER-ID"], $_SESSION["X-USER-AGENT"]) || $_SESSION['X-USER-AGENT'] !== $_SERVER['HTTP_USER_AGENT']
                    || $_COOKIE['sseed'] !== $user_data['session_hash']) {
                    foreach ($this->reserved as $key => $value)
                        unset($_SESSION[$key]);
                } else {
                    $user_data = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("id" => $_SESSION["X-USER-ID"]));
                    User::$user = new User($user_data);
                }
            } else {
                new SSeedAuth();
            }
        }
    }
}