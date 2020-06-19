<?php

spl_autoload_register(function ($class_name) {
    $filename = $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/" . str_replace('\\', "/", $class_name) . '.php';
    require_once $filename;
});
function class_exists_e($class_name) {
    $filename = $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/" . str_replace('\\', "/", $class_name) . '.php';
    return file_exists($filename);
}

define('REGEX' , array(
    'name' => "/^[A-Za-zА-Яа-я]{1,32}$/u",
    'second_name' => "/^[A-Za-zА-ЯЁа-яё\-]{1,32}$/u",
    'login' => "/^[A-Za-zA-Za-z0-9\.]{3,31}$/",
    'group' => "/^[в]?[0-9]{5,6}\/[0-9]{1,5}$/",
    'password' => "/^(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?!.*\s).*$/",
    'password_confirm' => "/^(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?!.*\s).*$/",
    'email' => "/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/",
    'number' => "/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/",
    'vk_link' => "/^https:\/\/vk.com\/[A-Za-z0-9_]{1,32}$/",
));

$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //DEBUG ONLY
];

$access = new \Configuration\Rights\AccessController();
$mysql = new \Configuration\Database\Connection("127.0.0.1", "root", "", "groups_sch", $options);
$session = new \Security\Session();





function is_assoc(array $array)
{
    // Keys of the array
    $keys = array_keys($array);

    // If the array keys of the keys match the keys, then the array must
    // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
    return array_keys($keys) !== $keys;
}
function assoc_to_arr(array $array) {
    return (is_assoc($array)) ? array($array) : $array;
}
function class_uses_deep($class, $autoload = true)
{
    $traits = [];

    // Get traits of all parent classes
    do {
        $traits = array_merge(class_uses($class, $autoload), $traits);
    } while ($class = get_parent_class($class));

    // Get traits of all parent traits
    $traitsToSearch = $traits;
    while (!empty($traitsToSearch)) {
        $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
        $traits = array_merge($newTraits, $traits);
        $traitsToSearch = array_merge($newTraits, $traitsToSearch);
    };

    foreach ($traits as $trait => $same) {
        $traits = array_merge(class_uses($trait, $autoload), $traits);
    }

    return array_unique($traits);
}


