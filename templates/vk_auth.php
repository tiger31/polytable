<?php

include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $local_modules_path . "/Connect.php";
include_once $local_modules_path . "/classes/User.php";
global $mysql;
$mysql->set_active(QUERY_USER_SELECT);
session_start();

$client_id = '6433445'; // ID приложения
$client_secret = 'FjfrJcqVSRSzCmhlbVKL'; // Защищённый ключ
//$redirect_uri= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$redirect_uri = 'http://localhost:81/templates/vk_auth.php'; // Адрес сайта

$url = 'http://oauth.vk.com/authorize';

$params = array(
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'display' => "popup",
    //scope, state
    'response_type' => 'code',
    'v' => '5.74'
);

echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Авторизироваться через VK</a></p>';

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
        $user_id = $token['user_id'];
        $user = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY,
            array("vk_link" => "https://vk.com/" . (is_int($user_id) ? "id" : "") . $user_id));
        $_SESSION['user'] = new User($user, $mysql);
        $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, array("ip" => $_SERVER["REMOTE_ADDR"], "id" => $user["id"]));
    } else {
        $_SESSION['user'] = null;
        session_destroy();
    }
}

?>