<?php

namespace User\Auth;

use User\Auth;

class Auth_mail extends Auth{

    public function __construct() {
        global $mysql;

        $client_id = '759857'; // ID приложения
        $client_secret = 'f52bfdda6aced720c350b71e165f3d7e'; // Защищённый ключ
        $redirect_uri = 'https://polytable.ru/auth.php?m=mail'; // Адрес сайта

        if (isset($_GET['code'])) {
            $params = array(
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code'],
                'redirect_uri'  => $redirect_uri
            );
            $url = 'https://connect.mail.ru/oauth/token';
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
                $sign = md5("app_id={$client_id}method=users.getInfosecure=1session_key={$tokenInfo['access_token']}{$client_secret}");
                $params = array(
                    'method'       => 'users.getInfo',
                    'secure'       => '1',
                    'app_id'       => $client_id,
                    'session_key'  => $tokenInfo['access_token'],
                    'sig'          => $sign
                );
                $userInfo = json_decode(file_get_contents('http://www.appsmail.ru/platform/api' . '?' . urldecode(http_build_query($params))), true);
                if (isset($userInfo[0]['uid'])) {
                    $userInfo = array_shift($userInfo);
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