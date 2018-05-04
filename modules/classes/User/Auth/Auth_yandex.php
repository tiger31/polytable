<?php

namespace User\Auth;

use User\Auth;

class Auth_yandex extends Auth{

    public function __construct() {
        global $mysql;

        $client_id = '8f519b6836c247d5af94d5b7f55edb24'; // ID приложения
        $client_secret = '93b6548918694bd996c402b1c317141a'; // Защищённый ключ

        if (isset($_GET['code'])) {
            $params = array(
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code'],
                'client_id'     => $client_id,
                'client_secret' => $client_secret
            );
            $url = 'https://oauth.yandex.ru/token';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);
            $tokenInfo = json_decode($result, true);
            if (isset($tokenInfo['access_token'])) {
                $params = array(
                    'format'       => 'json',
                    'oauth_token'  => $tokenInfo['access_token']
                );
                $userInfo = json_decode(file_get_contents('https://login.yandex.ru/info' . '?' . urldecode(http_build_query($params))), true);
                if (isset($userInfo['id'])) {
                    for ($i = 0; $i < sizeof($userInfo['emails']); $i++) {
                        $email = $userInfo['emails'][$i];
                        $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("email" => $email));
                        if ($user) {
                            $this->auth($user['login']);
                            die();
                        }
                    }
                }
            }
        }
        static::redirect();
    }

}