<?php
include_once "modules/Config.php";

use Configuration\Validation\ConfirmHashHandler;

$handler = new ConfirmHashHandler();

?>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?=($handler->valid) ? "Вы успешно зарегистрированы"
                : "Oops... что-то пошло не так"
            ?></title>
        <link rel="stylesheet" type="text/css" href="css/register.css">
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
    </head>
    <body style="background-color: var(--grayGreen)">
    <table style="width: 100%; height: 100%;">
        <tr>
            <td>
                <div id="confirm">
                    <div id="logo"><img src="assets/images/Pi.png"></div>
                    <div id="response_title"><?=($handler->valid) ? "Успешно" : "Ошибка";?></div>
                    <div id="info">
                        <?=($handler->valid) ? "Аккаунт подтвежден"
                         : (($handler->excided) ? "Истекло время действия ссылки" : "Ссылка не действительна или время ее действия истекло")
                        ?>
                    </div>
                    <a href="index.php">На главную</a>
                </div>
            </td>
        </tr>
    </table>
    </body>
    </html>