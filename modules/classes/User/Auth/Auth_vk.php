<?php

namespace User\Auth;

use User\Auth;

class Auth_vk extends Auth{

    function __construct() {
        global $mysql;

        $client_id = '6463991'; // ID приложения
        $client_secret = 'R5MRskuOucoUzHPE6s4T'; // Защищённый ключ
        $redirect_uri = 'https://polytable.ru/auth.php?m=vk'; // Адрес сайта

        if (isset($_GET['code'])) {
            $params = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'code' => $_GET['code'],
                'redirect_uri' => $redirect_uri
            );
            $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);
            // всё ок, можем работать с API
            if (isset($token['access_token'])) {
                $user_id = $token['user_id'];
                $user = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("vk_id" => $user_id));
                if ($user) {
                    $this->auth($user['login']);
                    die();
                }
            }
        }
        static::redirect();
    }

}