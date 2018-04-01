<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    function session_check($destroy = false) {
        if (session_status() !== 2 or !isset($_SESSION['user']) or $_SESSION['user'] == null or !($_SESSION['user'] instanceof User)
            or $_SESSION['HTTP_USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']) {
            if (session_status() === 2 and $destroy)
                session_destroy();
            return false;
        } else {
            return true;
        }
    }
    function escape_string($str) {
        return htmlspecialchars(htmlentities(strip_tags($str), ENT_QUOTES), ENT_QUOTES);
    }
    function set_csrf_token() {
        $hash = md5(generateRandomString());
        setcookie("X-CSRF-token", $hash, time()+3600*24*30);
        header("X-CSRF-token: " . $hash);
    }

