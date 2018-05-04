<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";

use User\User;

$client_id = '6463991'; // ID приложения
$client_secret = 'FjfrJcqVSRSzCmhlbVKL'; // Защищённый ключ
$redirect_uri = 'https://polytable.ru/link.php'; // Адрес сайта

$user = User::$user;
if ($user) {
    $login = $user->login;
    if (isset($_GET['code'])) {
        $result = false;
        $params = array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri
        );
        $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);
        // всё ок, можем работать с API
        if (isset($token['access_token'])) {
            $vk_id = $token['user_id'];
            $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, array("login" => $login, "vk_id" => $vk_id));
        }
    } else if (isset($_GET['unlink']) && $_GET['unlink'] === "1") {
        $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, array("login" => $login, "vk_id" => null));
    }
}
header('Location: ' . ((isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "profile.php")));
die();
