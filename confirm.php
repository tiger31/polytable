<?php
include_once "modules/Connect.php";
include_once "modules/Security.php";
global $mysql;
$mysql->set_active(
        QUERY_CONFIRM_SELECT,
        QUERY_USER_UPDATE,
        QUERY_USER_SELECT,
        QUERY_CONFIRM_DELETE
);

session_start();
$valid = true;
$hash_excided = false;
if (isset($_GET['login']) and isset($_GET['hash'])) {
    if (session_check())
        header("Location: " . $default_redirect);
    $data = $mysql->exec(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_GET['login']));
    if (!$data && $valid) {
        $valid = false;
    } else if ($valid) {
        $hash = $mysql->exec(QUERY_CONFIRM_SELECT, RETURN_FALSE_ON_EMPTY, array("login" => $_GET['login'], "type" => "ACCOUNT"));
        $now = new DateTime("now");
        $hash_lifetime = ($hash) ? new DateTime($hash['lifetime']) : null;
        if (!$hash || $now > $hash_lifetime) {
            $hash_excided = true;
            $valid = false;
        } else {
            if ($_GET['hash'] === $hash['value']) {
                $mysql->exec(QUERY_USER_UPDATE, RETURN_IGNORE, array("login" => $data['login']));
                $mysql->exec(QUERY_CONFIRM_DELETE, RETURN_IGNORE, array("login" => $_GET['login'], "type" => "ACCOUNT"));
                include_once "modules/ImageCreatePattern.php";
                ImageCreatePattern::call($data["id"]);
            } else {
                $valid = false;
            }
        }
    }
} else {
    $valid = false;
}
?>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Придумайте пароль</title>
        <link rel="stylesheet" type="text/css" href="css/register.css">
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
    </head>
    <body style="background-color: var(--grayGreen)">
    <table style="width: 100%; height: 100%;">
        <tr>
            <td>
                <div id="confirm">
                    <div id="logo"><img src="assets/images/Pi.png"></div>
                    <div id="response_title"><?=($valid) ? "Успешно" : "Ошибка";?></div>
                    <div id="info">
                        <?=($valid) ? "Аккаунт подтвежден"
                         : (($hash_excided) ? "Истекло время действия ссылки" : "Ссылка не действительна или время ее действия истекло")
                        ?>
                    </div>
                    <a href="index.php">На главную</a>
                </div>
            </td>
        </tr>
    </table>
    </body>
    </html>