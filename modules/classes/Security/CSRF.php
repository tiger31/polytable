<?php

namespace Security;
    class CSRF {
        static function set_csrf_token() {
            $_SESSION['X-CSRF-TOKEN'] = md5(Shield::rnd_str()) . md5(Shield::rnd_str());
            //На релизе заменить secure на true и снизить lifetime
            setcookie('X-CSRF-TOKEN', $_SESSION['X-CSRF-TOKEN'], time() + 3600 * 24 * 30, "", "", false);
        }

        static function verify_csrf_token($token) {
            $verified = ($token === $_SESSION['X-CSRF-TOKEN'] && $token === $_COOKIE['X-CSRF-TOKEN']);
            if ($verified)
                static::set_csrf_token();
            return $verified;
        }

        static function unset() {
            unset($_SESSION['X-CSRF-TOKEN']);
            setcookie('X-CSRF-TOKEN', "", 1, "", "", false);
        }
    }

