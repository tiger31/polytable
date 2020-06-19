<?php

namespace Configuration\Validation;

use Files\Images\ImageCreatePattern;

class ConfirmHashHandler {

    public $valid = false;
    public $excided = false;

    public $target;

    public function __construct() {
        global $mysql;
        if (isset($_GET['login'], $_GET['hash'])) {
            $user = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_GET['login']));
            $hash = $mysql(QUERY_CONFIRM_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_GET['login'], "hash" => $_GET['hash']));
            if ($hash) {
                $this->target = $hash['for'];
                $now = new \DateTime("now");
                $hash_lifetime = new \DateTime($hash['lifetime']);
                if ($now > $hash_lifetime) {
                    $this->excided = true;
                } else {
                    switch ($hash['for']) {
                        case ConfirmHash::ACCOUNT:
                            $mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array("login" => $hash['login']));
                            ImageCreatePattern::call($user['id']);
                            break;
                        case ConfirmHash::PASSWORD:
                            $mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array("login" => $user['login'], "pass" => $hash['stored']));
                            //TODO Переставить куку
                            break;
                        case ConfirmHash::EMAIL:
                            $mysql(QUERY_USER_UPDATE, RETURN_IGNORE, array("login" => $user['login'], "email" => $hash['stored']));
                            break;
                    }
                    $this->valid = true;
                }
                $mysql(QUERY_CONFIRM_DELETE, RETURN_IGNORE, array("login" => $_GET['login'], "hash" => $_GET['hash']));
            }
        }
    }

}