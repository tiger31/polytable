<?php
$local_modules_path = $_SERVER['DOCUMENT_ROOT'] . "/modules";
$default_redirect = $_SERVER['HTTP_HOST'] . "/index.php";

define('RIGHTS' ,array(
    'USER' => 0,
    'MODERATOR' => 1,
    'ADMINISTRATOR' => 2
));
define('REGEX' ,array(
    'name' => "/^[A-Za-zА-Яа-я]{1,32}$/u",
    'second_name' => "/^[A-Za-zА-ЯЁа-яё\-]{1,32}$/u",
    'login' => "/^[A-Za-zA-Za-z0-9\.]{3,31}$/",
    'group' => "/^[в]?[0-9]{5,6}\/[0-9]{1,5}$/",
    'password' => "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
    'password_confirm' => "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
    'is_head' => "/type[12]/",
    'email' => "/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/",
    'number' => "/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/",
    'vk_link' => "/^https:\/\/vk.com\/[A-Za-z0-9_]{1,32}$/",
    'submit_request' => "//"
));
define('USER_MODULES', array(
    'default' => array (
        'menu_title' => "Настройки аккаунта",
        'menu_icon' => "setting",
        'modules' => array(
            'UserInfo' => array(
                'module' => true,
                'menu' => true,
                'module_name' => 'user_info',
                'menu_group' => 'default',
                'chain' => "main"
            )
        )
    ),
    'group' => array(
        'menu_title' => 'Моя группа',
        'menu_icon' => 'users',
        'modules' => array(
            'GroupStats' => array(
                'module' => true,
                'menu' => true,
                'module_name' => 'group_stats',
                'menu_group' => 'group',
                'chain' => 'main'
            ),
            'GroupCache' => array(
                'module' => true,
                'menu' => true,
                'module_name' => 'group_cache',
                'menu_group' => 'group',
                'chain' => 'main'
            ),
            'GroupHead' => array(
                'module' => true,
                'module_name' => 'group_head',
                'menu_group' => 'group',
                'chain' => 'main'
            )
        ),
    ),
    'head' => array(
        'menu_title' => 'Староста',
        'menu_icon' => 'student',
        'modules' => array(
        )
    )
));
define('API_CALLS', array(
    "check" => array(
        "method" => "GET",
        "mysql" => array("user_check"),
        "fields" => array("field", "value"),
        "isset" => true,
        "regex" => false,
        "user" => false,
        "rights" => false,
        "group" => false
    ),
    "send" => array(
        "method" => "GET",
        "mysql" => array("homework_check", "file_select", "file_upload", "homework_insert", "homework_update"),
        "fields" => array("date", "lesson", "text"),
        "isset" => true,
        "regex" => false,
        "user" => array(
            "logged" => true
        ),
        "rights" => false,
        "group" => "editor"
    ),
    "register" => array(
        "method" => "GET",
        "mysql" => array("user_insert", "email_select"),
        "fields" => ['login', 'group', 'email', 'password', 'password_confirm', 'submit_request'],
        "isset" => true,
        "regex" => array(
            "allow_empty" => false
        ),
        "user" => array(
            "logged" => false
        ),
        "rights" => false,
        "group" => false
    ),
    "get" => array(
        "method" => "GET",
        "mysql" => array("homework_select"),
        "fields" => ["date", "lesson"],
        "isset" => true,
        "regex" => false,
        "user" => array(
            "logged" => true
        ),
        "rights" => false,
        "group" => "editor"
    ),
    "upload" => array(
        "method" => "POST",
        "mysql" => false,
        "fields" => false,
        "isset" => false,
        "regex" => false,
        "user" => array(
            "logged" => true
        ),
        "rights" => false,
        "group" => "editor"
    ),
    "editor" => array(
        "method" => "GET",
        "mysql" => array("user_select", "contributor_insert", "contributor_check", "contributor_delete"),
        "fields" => ["login", "event"],
        "isset" => true,
        "regex" => false,
        "user" => array(
            "logged" => true
        ),
        "rights" => false,
        "group" => "head"
    ),
    "cache" => array(
        "method" => "GET",
        "mysql" => array("group_update", "group_select"),
        "fields" => ["subject"],
        "isset" => true,
        "regex" => false,
        "user" => array(
            "logged" => true,
        ),
        "rights" => false,
        "group" => "editor"
    ),
    "update" => array(
        "method" => "GET",
        "mysql" => array("user_update"),
        "fields" => ["email", "number", "vk_link"],
        "isset" => true,
        "regex" => array(
            "allow_empty" => true
        ),
        "user" => array(
            "logged" => true
        ),
        "rights" => false,
        "group" => false
    ),
    "calendar" => array(
        "method" => "GET",
        "mysql" => array("calendar_select"),
        "fields" => ["group", "day", "number"],
        "isset" => false,
        "regex" => false,
        "user" => false,
        "rights" => false,
        "group" => false
    )
));
//Function for generating random salt
function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
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