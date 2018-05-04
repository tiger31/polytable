<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";

if (\User\User::$user /*|| $_SERVER['SERVER_PORT'] != 443 */) \User\Auth::redirect();
$method = "User\\Auth\\Auth_". $_GET["m"];
if (class_exists_e($method))
    new $method();
\User\Auth::redirect();
