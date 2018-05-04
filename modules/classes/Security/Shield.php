<?php declare(strict_types=1);

namespace Security;

    use User;

    class Shield {
        static function rnd_str($length = 16) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        static function escape_str($str) {
            return htmlspecialchars(htmlentities(strip_tags($str), ENT_QUOTES), ENT_QUOTES);
        }

        static function session_check($destroy = false) {
            if (session_status() !== 2 or !isset($_SESSION['user']) or $_SESSION['user'] == null or !($_SESSION['user'] instanceof User\User) or $_SESSION['HTTP_USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']) {
                if (session_status() === 2 and $destroy)
                    session_destroy();
                return false;
            } else {
                return true;
            }
        }
    }
