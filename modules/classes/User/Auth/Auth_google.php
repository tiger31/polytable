<?php

namespace User\Auth;

use User\Auth;

class Auth_google extends Auth {

    public function __construct() {
        global $mysql;

        $client_id = ''; // ID приложения
        $client_secret = ''; // Защищённый ключ
        $redirect_uri = 'https://polytable.ru/auth.php?m=google'; // Адрес сайта

        if (isset($_GET['code'])) {
            $params = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
                'code' => $_GET['code']
            );
            $url = 'https://accounts.google.com/o/oauth2/token';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $result = curl_exec($curl);
            curl_close($curl);
            $tokenInfo = json_decode($result, true);
            if (isset($tokenInfo['access_token'])) {
                $params['access_token'] = $tokenInfo['access_token'];
                $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);
                if (isset($userInfo['id'])) {
                    $email = $userInfo['email'];
                    $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("email" => $email));
                    if ($user) {
                        $this->auth($user['login']);
                        die();
                    }
                }
            }
        }
        static::redirect();
    }
}
