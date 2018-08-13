<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";

if (\User\User::$user !== null  /* ||  $_SERVER['SERVER_PORT'] != 443 */) {
    header("Location: index.php");
    die();
}
$method = "User\\Auth\\Auth_". $_GET["m"];
if (class_exists_e($method))
    new $method();
header("Location: index.php");
die();
